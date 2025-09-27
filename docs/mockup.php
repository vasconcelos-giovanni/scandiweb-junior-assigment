<?

// Query
$products = Procuct::with(['dvd', 'book'])->all();

// Response collection
$products = [
        [
            'sku' => '12345',
            'title' => 'Inception',
            'price' => 19.99,
            'type' => 'dvd',
            'dvd' => [
                'size' => 700
            ],
            'book' => null
        ],
        [
            'sku' => '67890',
            'title' => 'The Great Gatsby',
            'price' => 14.99,
            'type' => 'book',
            'book' => [
                'weight' => 1.2,
            ],
            'dvd' => null
        ]
    ];

Dvd::all();
Book::all();