# Personal Portfolio — Laravel Backend

Admin panel and REST API for a personal portfolio website. Manage your profile, projects, blog, skills, experience and more from the admin panel; a frontend (built with Next.js) reads everything through the `/api/v1` JSON API.

**Frontend:** coming soon

## Features

- **Admin panel** (Bootstrap): profile, projects, blog posts, categories, skills, services, experience, education, training, certifications, testimonials, team, media, contact messages, newsletter subscribers (Excel export), settings, analytics
- **REST API** (`/api/v1`): read-only portfolio data plus contact form and newsletter endpoints
- Rate limiting (60 requests/minute), filtering, search and pagination on the API
- Postman collection included: [`docs/portfolio-api-postman-collection.json`](docs/portfolio-api-postman-collection.json)

## Requirements

- PHP 8.3+
- Composer 2
- Node.js 20.19+ and npm
- SQLite (default) or MySQL

## Installation

```bash
git clone https://github.com/makgr/personalportfolio.git
cd personalportfolio

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
php artisan storage:link

php artisan serve
```

Open http://localhost:8000/login and sign in with the seeded account:

- **Email:** `test@example.com`
- **Password:** `password`

Change this password right after your first login.

To use MySQL instead of SQLite, set `DB_CONNECTION=mysql` and the `DB_*` values in `.env` before running `migrate`.

## API

Base URL: `http://localhost:8000/api/v1`

| Method | Endpoint |
|---|---|
| GET | `/profile` |
| GET | `/projects`, `/projects/featured`, `/projects/{slug}` |
| GET | `/blog-posts`, `/blog-posts/featured`, `/blog-posts/{slug}` |
| GET | `/categories`, `/categories/type/{type}`, `/categories/{slug}` |
| GET | `/skills` |
| GET | `/services` |
| GET | `/experiences` |
| GET | `/education` |
| GET | `/training`, `/training/featured` |
| GET | `/testimonials`, `/testimonials/featured` |
| GET | `/teams`, `/teams/featured`, `/teams/{slug}` |
| GET | `/settings`, `/settings/{key}` |
| POST | `/contact` |
| POST | `/newsletter/subscribe`, `/newsletter/unsubscribe` |

Every response has the shape:

```json
{ "success": true, "data": [], "message": "..." }
```

Import the Postman collection to try every endpoint with example parameters. Its `base_url` variable defaults to `http://localhost:8000`.

## Testing

```bash
php artisan test
```

## Contributing

Issues and pull requests are welcome. Please open an issue first for larger changes.

## License

[MIT](LICENSE)
