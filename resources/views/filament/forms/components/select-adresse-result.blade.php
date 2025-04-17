<div>
    <div class="font-meduim">{{$adresse->country->name}}</div>
    <div class="font-meduim">{{$adresse->city->name}}</div>
    <div>{{$adresse->district->name}}</div>
    <div>{{$adresse->quartier->name ?? ""}}</div>
    <div class="text-grey text-xs">{{$adresse->address}}</div>
</div>

<style>
    .font-meduim {
        font-weight: 500;
    }

    .text-grey {
        color: #6b7280;
    }
</style>