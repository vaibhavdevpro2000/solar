<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
@vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body>
    <h1 class="text-3xl font-bold underline text-blue-600">
        Hello Laravel with Tailwind and jQuery!
    </h1>

    <button id="test-btn" class="mt-4 px-4 py-2 bg-black text-white rounded">
        Click Me
    </button>

    <script type="module">
        $(document).ready(function() {
            $('#test-btn').on('click', function() {
                alert('jQuery works with Tailwind!');
            });
        });
    </script>
</body>
</html>