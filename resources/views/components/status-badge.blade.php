@php

$config = match ($status) {

    'submitted' => [
        'label' => 'Submitted',
        'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'icon' => '🟡',
    ],

    'verified' => [
        'label' => 'Verified',
        'class' => 'bg-green-100 text-green-800 border-green-200',
        'icon' => '🟢',
    ],

    'rejected' => [
        'label' => 'Rejected',
        'class' => 'bg-red-100 text-red-800 border-red-200',
        'icon' => '🔴',
    ],

    default => [
        'label' => ucfirst($status),
        'class' => 'bg-gray-100 text-gray-800 border-gray-200',
        'icon' => '⚪',
    ],

};

@endphp

<span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-medium rounded-full border {{ $config['class'] }}">
    <span>{{ $config['icon'] }}</span>
    <span>{{ $config['label'] }}</span>
</span>
