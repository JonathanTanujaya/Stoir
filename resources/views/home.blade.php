<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Route Explorer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Laravel Route Explorer</h1>
            <p class="text-gray-600">Explore and test your application routes</p>
        </div>

        <!-- Routes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($routes as $route)
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                <!-- Route Info -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-mono bg-gray-100 px-2 py-1 rounded text-gray-700">
                            {{ $route['uri'] }}
                        </span>
                    </div>
                    <!-- Methods -->
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach($route['methods'] as $method)
                            @if($method !== 'HEAD')
                            <span class="text-xs px-2 py-1 rounded font-semibold
                                @if($method === 'GET') bg-green-100 text-green-800
                                @elseif($method === 'POST') bg-blue-100 text-blue-800
                                @elseif($method === 'PUT') bg-yellow-100 text-yellow-800
                                @elseif($method === 'DELETE') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $method }}
                            </span>
                            @endif
                        @endforeach
                    </div>
                    <!-- Route Name -->
                    @if($route['name'])
                    <div class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">Name:</span> {{ $route['name'] }}
                    </div>
                    @endif
                </div>
                
                <!-- Try It Button -->
                <button 
                    onclick="tryRoute('{{ $route['uri'] }}', '{{ $route['methods'][0] }}', this)"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <span class="button-text">Try It</span>
                    <span class="loading-text hidden">Loading...</span>
                </button>
                <!-- Response Container -->
                <div class="response-container mt-4 hidden">
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-800 mb-2">Response:</h4>
                        <div class="response-content bg-gray-50 p-3 rounded text-sm font-mono overflow-x-auto max-h-40 overflow-y-auto"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @if($routes->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-500 text-lg">No routes found</div>
        </div>
        @endif
    </div>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Testing route...</span>
            </div>
        </div>
    </div>
    <script>
        // Set up CSRF token for all requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        async function tryRoute(uri, method, button) {
            const card = button.closest('.bg-white');
            const responseContainer = card.querySelector('.response-container');
            const responseContent = card.querySelector('.response-content');
            const buttonText = button.querySelector('.button-text');
            const loadingText = button.querySelector('.loading-text');
            
            // Handle different route types
            let actualUri = uri;
            let isTestable = true;
            
            // Check if route has parameters
            if (uri.includes('{') && uri.includes('}')) {
                // Check for optional parameter routes (single parameter that we can test)
                if (uri === 'api/areas/{kodeDivisi}' || 
                    uri === 'api/barang/{kodeDivisi}' || 
                    uri === 'api/customers/{kodeDivisi}' ||
                    uri === 'api/kategori/{kodeDivisi}' ||
                    uri === 'api/sales/{kodeDivisi}') {
                    // Use default test value
                    actualUri = uri.replace('{kodeDivisi}', '01');
                    isTestable = true;
                } else if (uri.includes('{master_user}')) {
                    actualUri = uri.replace('{master_user}', '1');
                    isTestable = true;
                } else if (uri.includes('{mdivisi}')) {
                    actualUri = uri.replace('{mdivisi}', '01');
                    isTestable = true;
                } else if (uri.includes('{mcoa}')) {
                    actualUri = uri.replace('{mcoa}', '1');
                    isTestable = true;
                } else if (uri.includes('{mdokumen}') || uri.includes('{mdokuman}')) {
                    actualUri = uri.replace('{mdokumen}', '01').replace('{mdokuman}', '01');
                    isTestable = true;
                } else if (uri.includes('{supplier}')) {
                    actualUri = uri.replace('{supplier}', '1');
                    isTestable = true;
                } else if (uri.includes('{kodeDivisi}/{kodeArea}')) {
                    // Use real data for all methods
                    actualUri = uri.replace('{kodeDivisi}', '01').replace('{kodeArea}', 'TST');
                    isTestable = true;
                } else if (uri.includes('{kodeDivisi}/{kodeBarang}')) {
                    // Use real data for all methods
                    actualUri = uri.replace('{kodeDivisi}', '01').replace('{kodeBarang}', '0411106013');
                    isTestable = true;
                } else if (uri.includes('{kodeDivisi}/{kodeCust}')) {
                    // Use real data for all methods
                    actualUri = uri.replace('{kodeDivisi}', '01').replace('{kodeCust}', '10ANA');
                    isTestable = true;
                } else if (uri.includes('{kodeDivisi}/{kodeKategori}')) {
                    actualUri = uri.replace('{kodeDivisi}', '01').replace('{kodeKategori}', 'AF');
                    isTestable = true;
                } else if (uri.includes('{kodeDivisi}/{kodeSales}')) {
                    actualUri = uri.replace('{kodeDivisi}', '01').replace('{kodeSales}', '001');
                    isTestable = true;
                } else {
                    // Routes with unsupported parameters
                    isTestable = false;
                }
            }
            
            if (!isTestable) {
                responseContainer.classList.remove('hidden');
                
                // Provide specific examples based on route pattern
                let examples = '';
                if (uri.includes('{kodeDivisi}/{kodeArea}')) {
                    examples = '<br><strong>Example:</strong> <code class="bg-blue-100 px-1 rounded">api/areas/01/01</code> (PALEMBANG)';
                } else if (uri.includes('{kodeDivisi}/{kodeBarang}')) {
                    examples = '<br><strong>Example:</strong> <code class="bg-blue-100 px-1 rounded">api/barang/01/0411106013</code>';
                } else if (uri.includes('{kodeDivisi}/{kodeCust}')) {
                    examples = '<br><strong>Example:</strong> <code class="bg-blue-100 px-1 rounded">api/customers/01/10ANA</code>';
                } else if (uri.includes('{kodeDivisi}/{kodeKategori}')) {
                    examples = '<br><strong>Example:</strong> <code class="bg-blue-100 px-1 rounded">api/kategori/01/AF</code>';
                } else if (uri.includes('{kodeDivisi}/{kodeSales}')) {
                    examples = '<br><strong>Example:</strong> <code class="bg-blue-100 px-1 rounded">api/sales/01/001</code>';
                } else {
                    examples = '<br><strong>Example:</strong> Replace parameters with actual values';
                }
                
                responseContent.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="text-yellow-800 text-sm">
                            <strong>Parameter Route:</strong> This route requires parameters.
                            ${examples}
                            <br><strong>Note:</strong> Use tools like Postman or provide actual parameter values to test this endpoint.
                        </p>
                    </div>
                `;
                return;
            }
            
            // Show loading state
            button.disabled = true;
            buttonText.classList.add('hidden');
            loadingText.classList.remove('hidden');
            button.classList.add('opacity-75');
            try {
                // Prepare request options
                const requestOptions = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                };
                
                // Show warning for POST requests instead of auto-generating dummy data
                if (method === 'POST') {
                    // Show response container with warning instead of making actual request
                    responseContainer.classList.remove('hidden');
                    responseContent.innerHTML = `
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                            <p class="text-yellow-800 text-sm">
                                <strong>⚠️ POST Request:</strong> This endpoint creates new data in the database.
                                <br><br><strong>To test safely:</strong>
                                <br>• Use tools like Postman or Thunder Client
                                <br>• Provide your own test data 
                                <br>• Ensure data won't conflict with existing records
                                <br><br><strong>Note:</strong> Auto-testing disabled to prevent dummy data creation.
                            </p>
                        </div>
                    `;
                    return;
                }

                // Show warning for PUT requests
                if (method === 'PUT') {
                    responseContainer.classList.remove('hidden');
                    responseContent.innerHTML = `
                        <div class="bg-orange-50 border border-orange-200 rounded p-3">
                            <p class="text-orange-800 text-sm">
                                <strong>⚠️ PUT Request:</strong> This endpoint updates existing data in the database.
                                <br><br><strong>To test safely:</strong>
                                <br>• Use tools like Postman or Thunder Client
                                <br>• Provide valid existing record identifiers
                                <br>• Include proper update data in request body
                                <br><br><strong>Note:</strong> Auto-testing disabled to prevent accidental data modification.
                            </p>
                        </div>
                    `;
                    return;
                }

                // Show warning for DELETE requests
                if (method === 'DELETE') {
                    responseContainer.classList.remove('hidden');
                    responseContent.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded p-3">
                            <p class="text-red-800 text-sm">
                                <strong>⚠️ DELETE Request:</strong> This endpoint permanently deletes data from the database.
                                <br><br><strong>To test safely:</strong>
                                <br>• Use tools like Postman or Thunder Client
                                <br>• Provide valid existing record identifiers
                                <br>• Be extremely careful with test data
                                <br><br><strong>Note:</strong> Auto-testing disabled to prevent accidental data deletion.
                            </p>
                        </div>
                    `;
                    return;
                }
                
                // Only add CSRF token for non-API routes
                if (!uri.startsWith('api/')) {
                    requestOptions.headers['X-CSRF-TOKEN'] = csrfToken;
                }
                
                // Make the request
                const response = await fetch(`/${actualUri}`, requestOptions);
                let responseData;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    responseData = await response.json();
                } else {
                    const textData = await response.text();
                    responseData = {
                        status: response.status,
                        statusText: response.statusText,
                        contentType: contentType,
                        data: textData.length > 500 ? textData.substring(0, 500) + '...' : textData
                    };
                }
                // Display response
                responseContent.innerHTML = `<pre class="whitespace-pre-wrap">${JSON.stringify(responseData, null, 2)}</pre>`;
                responseContainer.classList.remove('hidden');
                // Add success styling
                if (response.ok) {
                    responseContent.classList.remove('text-red-600');
                    responseContent.classList.add('text-green-600');
                } else {
                    responseContent.classList.remove('text-green-600');
                    responseContent.classList.add('text-red-600');
                }
            } catch (error) {
                // Display error
                responseContent.innerHTML = `<pre class="text-red-600">${JSON.stringify({
                    error: 'Request failed',
                    message: error.message,
                    type: error.name
                }, null, 2)}</pre>`;
                responseContainer.classList.remove('hidden');
            } finally {
                // Reset button state
                button.disabled = false;
                buttonText.classList.remove('hidden');
                loadingText.classList.add('hidden');
                button.classList.remove('opacity-75');
            }
        }
        
        // Handle responsive behavior
        window.addEventListener('resize', function() {
            // Close all open responses on mobile when rotating
            if (window.innerWidth < 768) {
                document.querySelectorAll('.response-container').forEach(container => {
                    if (!container.classList.contains('hidden')) {
                        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }
        });
    </script>
</body>
</html>
