<script src="{{ asset('css/pub.css') }}"></script>

<div>
    <div class="flex justify-between gap-2">
    @foreach ($this->getActions() as $action)
        {{ $action }}
    @endforeach
</div>
       <br>
        {{ $this->form }}
        <br>
     
<div id="printable" class="hidden">
    @include('impression.form-etat')
</div>
</div>


<script src="{{ asset('js/pub.js') }}"></script>
