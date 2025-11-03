<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GENAF-App Development Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">GENAF-App Development Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Database Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h2 class="text-xl font-semibold text-blue-800 mb-3">Database Status</h2>
                    <button onclick="checkStatus()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Check Status
                    </button>
                    <div id="status-result" class="mt-3 text-sm"></div>
                </div>

                <!-- Run Migrations -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h2 class="text-xl font-semibold text-green-800 mb-3">Run Migrations</h2>
                    <button onclick="runMigrations()"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Run Migrations
                    </button>
                    <div id="migration-result" class="mt-3 text-sm"></div>
                </div>

                <!-- Seed Database -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h2 class="text-xl font-semibold text-yellow-800 mb-3">Seed Database</h2>
                    <button onclick="seedDatabase()"
                        class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
                        Seed Database
                    </button>
                    <div id="seed-result" class="mt-3 text-sm"></div>
                </div>
            </div>

            <!-- Results Area -->
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Results</h2>
                <div id="results" class="bg-gray-50 border border-gray-200 rounded-lg p-4 min-h-32">
                    <p class="text-gray-500">Click a button above to see results...</p>
                </div>
            </div>

            <!-- Warning -->
            <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-red-800">⚠️ Development Only</h3>
                <p class="text-red-700 mt-2">
                    This dashboard is for development purposes only. Make sure to remove these routes before deploying
                    to production.
                </p>
            </div>
        </div>
    </div>

    <script>
        function checkStatus() {
            document.getElementById('status-result').innerHTML = 'Checking...';

            fetch('/dev/status')
                .then(response => response.json())
                .then(data => {
                    const statusDiv = document.getElementById('status-result');
                    const resultsDiv = document.getElementById('results');

                    if (data.error) {
                        statusDiv.innerHTML = '<span class="text-red-600">Error: ' + data.error + '</span>';
                        resultsDiv.innerHTML = '<pre class="text-red-600">' + JSON.stringify(data, null, 2) + '</pre>';
                    } else {
                        const connectionStatus = data.database_connection ?
                            '<span class="text-green-600">✓ Connected</span>' :
                            '<span class="text-red-600">✗ Not Connected</span>';

                        statusDiv.innerHTML = connectionStatus;

                        let html = '<h3 class="font-semibold mb-2">Database Status:</h3>';
                        html += '<p><strong>Connection:</strong> ' + connectionStatus + '</p>';
                        html += '<p><strong>Tables:</strong> ' + data.tables.length + ' tables</p>';
                        html += '<p><strong>Migrations:</strong> ' + data.migrations.length + ' migrations</p>';

                        if (data.tables.length > 0) {
                            html +=
                            '<h4 class="font-semibold mt-3 mb-1">Tables:</h4><ul class="list-disc list-inside">';
                            data.tables.forEach(table => {
                                html += '<li>' + table + '</li>';
                            });
                            html += '</ul>';
                        }

                        resultsDiv.innerHTML = html;
                    }
                })
                .catch(error => {
                    document.getElementById('status-result').innerHTML = '<span class="text-red-600">Error: ' + error
                        .message + '</span>';
                });
        }

        function runMigrations() {
            document.getElementById('migration-result').innerHTML = 'Running migrations...';

            fetch('/dev/migrate')
                .then(response => response.json())
                .then(data => {
                    const migrationDiv = document.getElementById('migration-result');
                    const resultsDiv = document.getElementById('results');

                    if (data.success) {
                        migrationDiv.innerHTML = '<span class="text-green-600">✓ Migrations completed</span>';

                        let html = '<h3 class="font-semibold mb-2 text-green-800">Migration Results:</h3>';
                        html += '<p class="text-green-600 mb-3">' + data.message + '</p>';
                        html += '<ul class="list-disc list-inside">';
                        data.results.forEach(result => {
                            html += '<li>' + result + '</li>';
                        });
                        html += '</ul>';

                        resultsDiv.innerHTML = html;
                    } else {
                        migrationDiv.innerHTML = '<span class="text-red-600">✗ Migration failed</span>';
                        resultsDiv.innerHTML = '<pre class="text-red-600">' + JSON.stringify(data, null, 2) + '</pre>';
                    }
                })
                .catch(error => {
                    document.getElementById('migration-result').innerHTML = '<span class="text-red-600">Error: ' + error
                        .message + '</span>';
                });
        }

        function seedDatabase() {
            document.getElementById('seed-result').innerHTML = 'Seeding database...';

            fetch('/dev/seed')
                .then(response => response.json())
                .then(data => {
                    const seedDiv = document.getElementById('seed-result');
                    const resultsDiv = document.getElementById('results');

                    if (data.success) {
                        seedDiv.innerHTML = '<span class="text-green-600">✓ Database seeded</span>';
                        resultsDiv.innerHTML =
                            '<h3 class="font-semibold mb-2 text-green-800">Seeding Results:</h3><p class="text-green-600">' +
                            data.message + '</p>';
                    } else {
                        seedDiv.innerHTML = '<span class="text-red-600">✗ Seeding failed</span>';
                        resultsDiv.innerHTML = '<pre class="text-red-600">' + JSON.stringify(data, null, 2) + '</pre>';
                    }
                })
                .catch(error => {
                    document.getElementById('seed-result').innerHTML = '<span class="text-red-600">Error: ' + error
                        .message + '</span>';
                });
        }
    </script>
</body>

</html>
