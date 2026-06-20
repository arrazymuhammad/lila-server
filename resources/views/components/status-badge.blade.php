@php
    $config = match ($status) {
        'submitted' => [
            'label' => 'Submitted',
            'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'dot' => 'bg-yellow-500',
        ],
        'verified' => [
            'label' => 'Verified',
            'class' => 'bg-green-100 text-green-800 border-green-200',
            'dot' => 'bg-green-500',
        ],
        'rejected' => [
            'label' => 'Rejected',
            'class' => 'bg-red-100 text-red-800 border-red-200',
            'dot' => 'bg-red-500',
        ],
        default => [
            'label' => ucfirst($status ?? 'unknown'),
            'class' => 'bg-gray-100 text-gray-800 border-gray-200',
            'dot' => 'bg-gray-400',
        ],
    };
@endphp

<span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-medium {{ $config['class'] }}">
    <span class="h-2 w-2 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
