<?php

return [
    'filterable_specifications' => [
        // T-shirt specifications
        'colors' => [
            // Can be simple strings like ['Orange'] or array of objects with 'name' and 'swatch_image'
            'type' => 'array_of_objects_or_strings',
            'display_name' => 'Colors',
        ],
        'size' => [
            'type' => 'array_of_strings', // E.g., ['L'], ['M', 'L', 'XL']
            'display_name' => 'Size',
        ],
        'gender' => [
            'type' => 'string', // E.g., 'رجالي', 'نسائي'
            'display_name' => 'Gender',
        ],
        'material' => [
            'type' => 'string', // E.g., 'قطن', 'Mesh and Fabric', 'Polyester', 'Solid Wood', 'جلد'
            'display_name' => 'Material',
        ],
        'neck_type' => [
            'type' => 'string', // E.g., 'Crew Neck', 'Polo Collar'
            'display_name' => 'Neck Type',
        ],
        'sleeve_length' => [
            'type' => 'string', // E.g., 'Short'
            'display_name' => 'Sleeve Length',
        ],
        // Furniture specifications
        'assembly_required' => [
            'type' => 'boolean', // E.g., true, false
            'display_name' => 'Assembly Required',
        ],
        'max_weight_kg' => [
            'type' => 'number', // E.g., 120
            'display_name' => 'Max Weight (kg)',
        ],
        'style' => [
            'type' => 'string', // E.g., 'Modern'
            'display_name' => 'Style',
        ],
        // Smartwatch specifications
        'display_type' => [
            'type' => 'string', // E.g., 'AMOLED'
            'display_name' => 'Display Type',
        ],
        'features' => [
            'type' => 'array_of_strings', // E.g., ['Heart Rate Monitor', 'Sleep Tracker', 'GPS'], ['الجلد المتين...', 'فتحة رئيسية...']
            'display_name' => 'Features',
        ],
        'water_resistance' => [
            'type' => 'string', // E.g., '5 ATM'
            'display_name' => 'Water Resistance',
        ],
        // Backpack specifications
        'capacity_liters' => [
            'type' => 'number', // E.g., 25
            'display_name' => 'Capacity (Liters)',
        ],
        'water_resistant' => [
            'type' => 'boolean', // E.g., true
            'display_name' => 'Water Resistant',
        ],
        // iPhone specifications
        'storage_gb' => [
            'type' => 'array_of_numbers_or_strings', // E.g., [128, 256, 512, 1024]
            'display_name' => 'Storage (GB)',
        ],
        'processor' => [
            'type' => 'string', // E.g., 'A17 Bionic', 'Intel Core i7-13700H'
            'display_name' => 'Processor',
        ],
        'display_size_inch' => [
            'type' => 'number', // E.g., 6.7
            'display_name' => 'Display Size (inch)',
        ],
        'camera_megapixels' => [
            'type' => 'string', // E.g., '48MP Main'
            'display_name' => 'Camera (Megapixels)',
        ],
        // Sport T-shirt specifications
        'fabric_type' => [
            'type' => 'string', // E.g., 'Breathable'
            'display_name' => 'Fabric Type',
        ],
        // Plain T-shirt specifications
        'fit_type' => [
            'type' => 'string', // E.g., 'Regular'
            'display_name' => 'Fit Type',
        ],
        // Laptop specifications (from Dell XPS 15)
        'ram' => [
            'type' => 'string', // E.g., '32GB DDR5'
            'display_name' => 'RAM',
        ],
        'storage' => [
            'type' => 'string', // E.g., '1TB PCIe NVMe SSD'
            'display_name' => 'Storage Type',
        ],
        'graphics_card' => [
            'type' => 'string', // E.g., 'NVIDIA GeForce RTX 4070'
            'display_name' => 'Graphics Card',
        ],
        'operating_system' => [
            'type' => 'string', // E.g., 'Windows 11 Pro'
            'display_name' => 'Operating System',
        ],
        'battery_life_hours' => [
            'type' => 'number', // E.g., 10
            'display_name' => 'Battery Life (Hours)',
        ],
        'webcam' => [
            'type' => 'string', // E.g., '1080p FHD'
            'display_name' => 'Webcam',
        ],
        'ports' => [
            'type' => 'string', // E.g., '2x Thunderbolt 4, 1x USB-C 3.2 Gen 2, SD Card Reader'
            'display_name' => 'Ports',
        ],
        'weight_kg' => [
            'type' => 'number', // E.g., 1.92
            'display_name' => 'Weight (kg)',
        ],
        // Sunglasses specifications
        'frame_material' => [
            'type' => 'string', // E.g., 'Acetate'
            'display_name' => 'Frame Material',
        ],
        'lens_type' => [
            'type' => 'string', // E.g., 'Polarized'
            'display_name' => 'Lens Type',
        ],
        'uv_protection' => [
            'type' => 'string', // E.g., 'UV400'
            'display_name' => 'UV Protection',
        ],
        'frame_shape' => [
            'type' => 'string', // E.g., 'Wayfarer'
            'display_name' => 'Frame Shape',
        ],
    ],
];