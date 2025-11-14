# Typing Game API - Bruno Collection

This is a Bruno collection for the Typing Game API endpoints.

## Setup

1. Install [Bruno](https://www.usebruno.com/)
2. Open Bruno and click "Open Collection"
3. Navigate to this folder: `bruno/typing-game`
4. Select the folder to load the collection

## Environment Setup

The collection includes a `Local` environment with the following variables:

- `base_url`: The base URL of your API (default: `http://localhost:8000`)
- `api_token`: Your Sanctum authentication token

### Getting an API Token

To test authenticated endpoints, you need a Sanctum token:

1. Register or login through your Laravel application
2. Generate a token for your user (you may need to create a token endpoint or use Laravel Tinker)
3. Update the `api_token` variable in the Local environment

Example using Tinker:
```bash
php artisan tinker
$user = User::find(1);
$token = $user->createToken('bruno-testing')->plainTextToken;
echo $token;
```

## Endpoints

### Public Endpoints (No Authentication Required)

- **Get Random Passage**: `GET /api/passages`
  - Fetches a random text passage

- **Get Passage By ID**: `GET /api/passages/{id}`
  - Fetches a specific passage by ID

- **Get Leaderboard**: `GET /api/leaderboard`
  - Retrieves the global leaderboard
  - Query params: `limit` (default: 10)

### Authenticated Endpoints (Requires Sanctum Token)

- **Submit Result**: `POST /api/game/submit`
  - Submits a typing test result
  - Body: `text_passage_id`, `wpm`, `accuracy`, `time_taken`, `errors_count`

- **Get User History**: `GET /api/user/history`
  - Retrieves the authenticated user's game history
  - Query params: `limit` (default: 20)

## Testing Flow

1. Start by fetching a random passage (`Get Random Passage`)
2. Note the passage ID
3. Authenticate and submit a result (`Submit Result`)
4. View your history (`Get User History`)
5. Check the leaderboard (`Get Leaderboard`)

## Notes

- All endpoints return JSON responses
- The API follows RESTful conventions
- Validation errors return 422 status codes
- Authentication errors return 401 status codes
