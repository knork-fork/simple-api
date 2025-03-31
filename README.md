# SimpleAPI

(Work in progress, API not yet functional)

Super lightweight CRUD wrapper for PostgreSQL with multi-tenant support and dynamic entity management.

### Installation

Start the container and build the image:

```bash
docker-compose up --build -d
```

Install dependencies using Composer:

```bash
docker/composer install --no-interaction
```

By default, the app will be available at: `http://localhost:11000`

If the image is already built, you can simply start the container:

```bash
docker-compose up -d
```

The container will persist through system restarts.
To stop and remove the container:

```bash
docker-compose down
```

### Usage

##### Identify as a New User

Generate your own secret which will be used to obtain access tokens. Store it securely — it acts as your private key.

You can create a secret manually, or generate one using a secure algorithm like this:
```bash
openssl rand -hex 32
```
It’s important that your secret is unique, strong, and never shared.

The server stores a hashed and salted version of your secret, assigns a unique user ID, and creates a dedicated schema for your data.

Same username and secret combination will always return a token for the same database schema, that is the pair always corresponds to same unique user id.

If you ever need to share access, share only the generated tokens, not your secret.
Tokens can be revoked at any time and may optionally include an expiration date.

> ⚠️ Secrets are used only for generating tokens. All other operations are done using the access tokens.

##### Get a User Token

Send a POST request to `/auth/token` with following payload:

```json
{
    "username": "your username or email",
    "secret": "your_secret",
    "description": "optional_token_description",
    "expires": "optional_token_expiration_date",
    "is_read_only": <true or false, default false>
}
```

If successful, you'll receive:

```json
{
    "token_id": "recognizable_hash_used_for_managing_tokens",
    "token": "your_token",
    "description": "token_description",
    "expires": "token_expiration_date"
}
```

> ⚠️ This is the only time the token will be shown in plain text.
Use token_id to revoke the token later if needed.

##### Register a New Resource Type

Send a POST request to `/types`:

```json
{
    "name": "resource_name"
}
```

This will create a new table (under your user schema).
An error will be returned if the resource type already exists.

##### Insert Data

Send a POST request to `/resources/{resource_name}`:

```json
{
    "data": {
        "field1": "value1",
        "field2": "value2",
        "field3": "value3"
    }
}
```

Data is stored as a JSONB object, so the structure is completely dynamic.

##### Query Data

- `GET /resources/{resource_name}` – fetch all data
- `GET /resources/{resource_name}/{id}` – fetch entry by ID
- `GET /resources/{resource_name}?field1=value1&field2=value2` – filter by fields

> ⚠️ Filtering a field for the first time may be slower.
The system caches filtered fields in a step table to speed up future queries.

### Documentation

See [API Documentation](doc/) in doc folder for more information.