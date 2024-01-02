<x-layouts.app>
    <style>
        .fi-ta-header-heading, .fi-ta-content > div.items-center, .fi-ta-header-ctn
        {
            display: none;
        }

        .fi-ta-content{
            margin-top: 10px;
        }

        .fi-ta-header {
            display: flex;
            flex-direction: column;
            padding: 1rem 2rem;
        }

        .fi-ta-group-header
        {
            border: 1px solid lightgray;
            padding-bottom: 20px;
        }

        .fi-ta-group-header >  .grid > h4
        {
            position: relative;
            margin: 10px 0px;
        }

    </style>
    <div class="px-2" style="margin-top: 10px">

        <div class="my-2">
            <h2 class="font-bold text-center text-lg"> {{\Carbon\Carbon::parse(request('date') ?? now())->format('d-m-Y l')}} <br> Incoming Events</h2>
        </div>

        @livewire(\App\Filament\Widgets\IncomingEventsWidget::class)
    </div>
</x-layouts.app>
