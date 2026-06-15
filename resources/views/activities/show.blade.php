@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <div
        class="bg-white rounded-xl shadow p-6"
    >
        <h2
            class="text-2xl font-bold"
        >
            {{ $session->title }}
        </h2>

        <div
            class="grid grid-cols-4 gap-4 mt-4"
        >

            <div>
                <div
                    class="text-sm text-gray-500"
                >
                    Status
                </div>

                <div>
                    {{ $session->status }}
                </div>
            </div>

            <div>
                <div
                    class="text-sm text-gray-500"
                >
                    Distance
                </div>

                <div>
                    {{ number_format($session->distance / 1000, 2) }} km
                </div>
            </div>

            <div>
                <div
                    class="text-sm text-gray-500"
                >
                    Start
                </div>

                <div>
                    {{ $session->start_time }}
                </div>
            </div>

            <div>
                <div
                    class="text-sm text-gray-500"
                >
                    End
                </div>

                <div>
                    {{ $session->end_time }}
                </div>
            </div>

        </div>
    </div>

    <div
        class="bg-white rounded-xl shadow overflow-hidden"
    >
        <div
            id="map"
            class="h-[600px]"
        ></div>
    </div>

    <div
        class="grid md:grid-cols-2 gap-6"
    >

        <div
            class="bg-white rounded-xl shadow p-6"
        >
            <h3
                class="font-semibold text-lg mb-4"
            >
                Events
            </h3>

            <div class="space-y-4">

                @foreach($session->events as $event)

                    <div
                        class="border rounded p-4"
                    >
                        <div
                            class="font-semibold"
                        >
                            {{ $event->title }}
                        </div>

                        <div
                            class="text-sm text-gray-500"
                        >
                            {{ $event->timestamp }}
                        </div>

                        <div
                            class="mt-2"
                        >
                            {{ $event->description }}
                        </div>
                    </div>

                @endforeach

            </div>
        </div>

        <div
            class="bg-white rounded-xl shadow p-6"
        >
            <h3
                class="font-semibold text-lg mb-4"
            >
                Photos
            </h3>

            <div
                class="grid grid-cols-3 gap-3"
            >

                @foreach(
                    $session->photos
                    as $photo
                )

                    <a
                        href="{{ asset($photo->file_path) }}"
                        target="_blank"
                    >

                        <img
                            src="{{ asset($photo->file_path) }}"
                            class="rounded-lg w-full h-32 object-cover"
                        >

                    </a>

                @endforeach

            </div>

        </div>

    </div>

</div>

<script>

const map = L.map(
    'map'
);

L.tileLayer(
    'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        maxZoom: 19
    }
).addTo(map);
L.popup({
    maxWidth: 500
})
const trackPoints =
    @json(
        $session->trackPoints
    );

const events =
    @json(
        $session->events
    );

const coordinates =
    trackPoints.map(
        point => [
            point.latitude,
            point.longitude
        ]
    );

const polyline =
    L.polyline(
        coordinates,
        {
            weight: 5
        }
    ).addTo(map);

events.forEach(event => {

    const firstPhoto =
        event.photos.length > 0
            ? `/${event.photos[0].file_path}`
            : 'https://placehold.co/400x250';

    const popup = `
        <div style="
            width:280px;
        ">

            <div style="
                font-weight:600;
                font-size:16px;
                margin-bottom:8px;
            ">
                ${event.title}
            </div>

            <img
                src="${firstPhoto}"
                style="
                    width:100%;
                    height:180px;
                    object-fit:cover;
                    border-radius:12px;
                    margin-bottom:10px;
                "
            >

            <div style="
                color:#6b7280;
                font-size:13px;
                margin-bottom:4px;
            ">
                ${new Date(
                    event.timestamp
                ).toLocaleDateString()}
            </div>

            <div style="
                font-size:14px;
                font-weight:500;
            ">
                ${event.photos.length} Foto
            </div>

        </div>
    `;

    L.marker([
        event.latitude,
        event.longitude,
    ])
    .addTo(map)
    .bindPopup(
        popup,
        {
            maxWidth: 320,
        }
    );

});

if (
    coordinates.length > 0
) {
    map.fitBounds(
        polyline.getBounds()
    );
}

</script>

@endsection
