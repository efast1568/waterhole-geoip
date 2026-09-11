# Waterhole GeoIP

A GeoIP extension for Waterhole.

**Status:** Development
**Tested with:** Waterhole 0.7, Waterhole 0.8

## Features

* Records the public IP address used when creating a post or comment.
* Resolves IP addresses to country codes using a configurable GeoIP provider.
* Caches resolved IP information in the database.
* Displays the posting country's flag in post and comment attribution.
* Allows users to enable or disable the IP flag displayed on their posts.
* Supports multiple GeoIP providers.
* Uses IP-API Free by default when no provider is explicitly configured.

## Requirements

* Waterhole
* PHP 8.2+
* A supported GeoIP provider

## Installation

Install the extension with Composer:

```bash
composer require subarist/waterhole-geoip
```

Run the database migrations:

```bash
php artisan migrate
```

Then clear the application cache:

```bash
php artisan optimize:clear
```

If the site is running under Laravel Octane, reload the workers:

```bash
php artisan octane:reload
```

## Configuration

The extension can be configured through environment variables in `.env`.

### Default configuration

No additional environment variables are required.

When no provider is explicitly configured, the extension automatically selects:

* `ipinfo` if `IPINFO_TOKEN` is set.
* `ip-api` if `IPINFO_TOKEN` is not set.

To explicitly use IP-API Free:

```env
GEOIP_PROVIDER=ip-api
```

### IP-API Free

IP-API Free does not require an API token.

```env
GEOIP_PROVIDER=ip-api
```

The extension uses:

```text
http://ip-api.com/json
```

Only the country code is requested.

### IPinfo

To use IPinfo, provide an IPinfo API token:

```env
GEOIP_PROVIDER=ipinfo
IPINFO_TOKEN=your_ipinfo_token
```

The extension uses the IPinfo Lite API to resolve the country code.

When IPinfo is configured as the active provider, the IPinfo attribution is displayed in the user's privacy settings.

## Provider selection

The active provider can be selected with:

```env
GEOIP_PROVIDER=ip-api
```

or:

```env
GEOIP_PROVIDER=ipinfo
IPINFO_TOKEN=your_ipinfo_token
```

If `GEOIP_PROVIDER` is not set, the provider is selected automatically based on whether `IPINFO_TOKEN` is configured.

### Example: IP-API Free

```env
GEOIP_PROVIDER=ip-api
```

### Example: IPinfo

```env
GEOIP_PROVIDER=ipinfo
IPINFO_TOKEN=your_ipinfo_token
```

After changing `.env`, clear the configuration cache:

```bash
php artisan optimize:clear
```

If Laravel Octane is enabled, reload the workers:

```bash
php artisan octane:reload
```

## User privacy setting

Users can control whether their posting country flag is displayed.

The setting is available in the user's profile/privacy settings as:

**IP Flag**

> Show the flag of the country I posted from, based on my IP address

The setting is enabled by default.

When disabled, the country flag is not displayed for that user's posts.

## IP data storage

The extension stores the original public IP address separately from the resolved country information.

### `content_ip`

Associates a post or comment with the IP address used to create it.

```text
content_type
content_id
ip_address
```

### `ip_info`

Stores the GeoIP result for each unique IP address.

```text
ip_address
country_code
```

The GeoIP provider is only queried when an IP address does not already have a corresponding entry in `ip_info`.

This prevents repeated API requests for the same IP address.

## Public IP addresses only

Private and reserved IP addresses are not sent to the GeoIP provider.

The extension validates IP addresses using:

```php
FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
```

Local, private, and reserved addresses are therefore ignored.

## GeoIP provider failures

GeoIP lookup failures do not prevent users from creating posts or comments.

If the provider:

* times out,
* returns an error,
* returns an invalid response,
* or cannot determine a country,

the post or comment is still created normally.

The country flag is simply unavailable until valid GeoIP information becomes available.

## Country flags

Country flags are generated from the two-letter ISO country code returned by the GeoIP provider.

Examples:

```text
TW → Taiwan
JP → Japan
US → United States
```

The flag image uses Waterhole's configured emoji CDN.

## Configuration reference

The extension configuration file is:

```text
config/waterhole-geoip.php
```

Default configuration:

```php
return [
    'provider' => env(
        'GEOIP_PROVIDER',
        env('IPINFO_TOKEN') ? 'ipinfo' : 'ip-api',
    ),

    'ip-api' => [
        'url' => 'http://ip-api.com/json',
    ],

    'ipinfo' => [
        'token' => env('IPINFO_TOKEN'),
    ],
];
```

## Privacy considerations

The extension stores the IP address associated with posts and comments in the database.

Administrators should ensure that their forum's privacy policy and applicable laws permit the collection and storage of IP addresses.

When a new IP address needs to be resolved, the public IP address may be sent to the configured GeoIP provider.

No GeoIP request is made when the IP address has already been resolved and cached in `ip_info`.

## License

See the license included with this extension.
