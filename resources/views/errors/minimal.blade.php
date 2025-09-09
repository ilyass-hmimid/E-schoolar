<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $statusCode = 500;
        $message = 'Something went wrong';
        
        if (isset($exception) && is_object($exception)) {
            $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
            $message = $exception->getMessage() ?: $message;
        } elseif (isset($errors) && $errors->any()) {
            $message = $errors->first();
        }
    @endphp
    <title>{{ $statusCode }} - Error</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .error-container {
            max-width: 600px;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .error-code {
            font-size: 2.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1.125rem;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        .back-link {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .back-link:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">
            {{ $statusCode }} - {{ $message }}
        </div>
        <div class="error-message">
            {{ $message }}
        </div>
        <a href="{{ url('/') }}" class="back-link">
            Go to Homepage
        </a>
    </div>
</body>
</html>
