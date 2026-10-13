# Mikoa API – Flutter usage

## Dependency

Add to `pubspec.yaml`:

```yaml
dependencies:
  http: ^1.1.0
```

## API

- **Endpoint:** `GET /api/mikoa/index`
- **Query:** `company_id=1` and/or `user_id=5`
- **Auth (optional):** `Authorization: Bearer <token>`

## Flutter function

Use `mikoa_api.dart`:

1. Set `baseUrl` to your server (e.g. `https://yourdomain.com`).
2. Call `fetchMikoa(companyId: 1)` or `fetchMikoa(userId: 5)`.

## Sample responses

**Success (200):** see `mikoa_sample_response.json`

**Error – missing company (400):** see `mikoa_sample_response_error.json`
