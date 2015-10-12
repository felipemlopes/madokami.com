@if($errors->any())
    <div class="ui negative message">
        <div class="header">Error</div>
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if(Session::has('success'))
    <?php $success = Session::get('success') ?>
    @if($success->any())
        <div class="ui positive message">
            <div class="header">Success</div>
            @foreach($success->all() as $message)
                <p>{{ $message }}</p>
            @endforeach
        </div>
    @endif
@endif