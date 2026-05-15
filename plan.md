# High-Level Implementation Plan: Move Gold Pricing to MetalpriceAPI and Display Gram Pricing

## Goal

Replace the current GoldAPI.io gold price integration with MetalpriceAPI, and make the dashboard and gold exchange flows display gold pricing in grams instead of ounces.

This plan is written for junior programmers and AI agents. Keep the implementation small, service-centered, and easy to verify.

## Current State

- Gold price fetching is centralized in `app/Services/GoldPriceService.php`.
- The service currently reads credentials and defaults from `config/services.php` under the `goldapi` key.
- Dashboard and exchange screens already consume the service result through fields such as `price_per_gram`, `price_per_ounce`, `currency`, and `timestamp`.
- The platform currently depends on GoldAPI.io response fields such as `price` and `price_gram_24k`.

## Target State

- The application fetches gold prices from MetalpriceAPI.
- MetalpriceAPI configuration is stored in `config/services.php`, using environment variables from `.env` and `.env.example`.
- The service converts the ounce-based price into a gram-based price before controllers or views receive it.
- Dashboard UI primarily displays the gram price in `IDR` currency format.
- Existing gold exchange logic continues to calculate purchased gold grams from saldo using `price_per_gram`.

## External API Notes

Use the MetalpriceAPI latest rates endpoint:

```text
https://api.metalpriceapi.com/v1/latest
```

Recommended query parameters:

```text
api_key=<METALPRICE_API_KEY>
base=USD
currencies=XAU
```

MetalpriceAPI returns rates in a `rates` object. For a `base=USD&currencies=XAU` response, the `rates.XAU` value represents how many ounces of gold equal 1 USD. The reciprocal gives the gold price per ounce:

```text
price_per_ounce = 1 / rates.XAU
```

If a response includes a direct `USDXAU` value, prefer it as the price per ounce and use the reciprocal as a fallback.

## Unit Conversion Rule

Product requirement:

```text
grams = ounces * 28.3495
```

For price conversion, apply the inverse relationship:

```text
price_per_gram = price_per_ounce / 28.3495
```

Define the conversion factor as a named constant in the service, for example:

```php
private const OUNCE_TO_GRAM = 28.3495;
```

Do not perform this conversion in Blade views or controllers. Keep it in `GoldPriceService`.

## Implementation Steps

1. Update configuration

- Replace or supplement the current `services.goldapi` configuration with `services.metalpriceapi`.
- Add environment variables to `.env.example`:
  - `METALPRICE_API_KEY=`
  - optionally `METALPRICE_API_BASE_URL=https://api.metalpriceapi.com/v1`
- Keep `default_metal` as `XAU`.
- Keep `default_currency` as `IDR` because the dashboard and exchange flow display rupiah values.

2. Refactor `GoldPriceService`

- Rename internal config reads from `services.goldapi.*` to `services.metalpriceapi.*`.
- Change the request URL from `/{metal}/{currency}` to `/latest`.
- Send the API key as either the `api_key` query parameter or `X-API-KEY` header. Prefer query parameter if that matches the MetalpriceAPI examples used by the team.
- Request `base=USD` and `currencies=XAU`.
- Parse the response safely:
  - Validate `success === true` when present.
  - Read `rates.USDXAU` if available.
  - Otherwise read `rates.XAU` and calculate `1 / rates.XAU`.
  - Guard against missing, zero, or non-numeric values.
- Return the same service contract currently used by controllers:
  - `price_per_gram`
  - `price_per_ounce`
  - `currency`
  - `timestamp`
- Calculate `price_per_gram` inside the service with `price_per_ounce / 28.3495`.
- Keep fallback behavior returning zero values if the API request fails.
- Update log messages from `GoldAPI` to `MetalpriceAPI`.

3. Review IDR conversion behavior

- The existing service converts USD prices to IDR through `ExchangeRateService` when the requested currency is `IDR`.
- Keep this behavior if the platform still needs rupiah display.
- Perform the ounce-to-gram conversion before currency conversion, then multiply both `price_per_ounce` and `price_per_gram` by the USD-to-IDR exchange rate.
- Make sure the returned `currency` field becomes `IDR` after conversion.

4. Update dashboard and exchange views

- Keep dashboard display focused on `price_per_gram`.
- Remove or de-emphasize any primary dashboard text that shows the gold price per ounce.
- If an ounce price remains visible for reference, label it clearly as secondary information.
- Confirm existing gold exchange create form uses `price_per_gram` for gram estimation.

5. Add or update tests

- Add a service test for a MetalpriceAPI response with `rates.USDXAU`.
- Add a service test for a MetalpriceAPI response with only `rates.XAU`, verifying the reciprocal calculation.
- Assert `price_per_gram = price_per_ounce / 28.3495`.
- Add an API failure test confirming the fallback structure remains stable.
- If IDR behavior is covered, mock `ExchangeRateService` and assert both ounce and gram prices are converted.

6. Manual verification

- Configure a local `METALPRICE_API_KEY`.
- Clear the gold price cache.
- Load the admin dashboard and nasabah dashboard.
- Confirm the displayed gold price is shown per gram.
- Open the gold exchange create page and confirm saldo-to-gram estimation still works.
- Confirm logs do not show failed GoldAPI calls.

## Suggested File Touch Points

- `app/Services/GoldPriceService.php`
- `config/services.php`
- `.env.example`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/nasabah/dashboard.blade.php`
- `resources/views/nasabah/gold-exchange/create.blade.php`
- `tests/Unit` or `tests/Feature`, depending on the existing test style

## Acceptance Criteria

- No runtime code references `goldapi.co` or GoldAPI.io configuration for gold price fetching.
- MetalpriceAPI is the only provider used by `GoldPriceService`.
- The service converts ounce-based API pricing into gram pricing.
- Dashboard gold price display is in grams.
- Gold exchange purchase estimation uses gram pricing.
- Automated tests cover successful API parsing, gram conversion, and fallback behavior.
- Existing dashboard and gold exchange routes continue to load without errors.

## Risks and Notes

- MetalpriceAPI rate semantics can be confusing because `rates.XAU` under `base=USD` is an amount of gold per 1 USD, not the USD price of 1 ounce. Use the reciprocal or `rates.USDXAU` for price per ounce.
- The product requirement uses `28.3495` as the ounce-to-gram factor. Apply that exact factor consistently, even if future business requirements decide to change the precision.
- Avoid moving conversion math into controllers or Blade templates. Keeping it in the service prevents duplicate and inconsistent calculations.
