<h1>Create Lot</h1>

@if (!empty($error))
    <div class="alert alert-danger">
        <ul>
            <li>{{ $error }}</li>
        </ul>
    </div>
@endif
<form method="post" action="{{ route('storeLot') }}">
    @csrf
    Currency: <br>
    <select name="currency_id">
        @foreach($currencies as $currency)
            <option value="{{ $currency['id'] }}">{{ $currency['name'] }}</option>
        @endforeach
    </select><br>
    {{--<input type="text" name="currency_id"><br>--}}
    Date time open: <br>
    <input type="text" name="date_time_open"><br>
    Date time close: <br>
    <input type="text" name="date_time_close"><br>
    Price: <br>
    <input type="text" name="price"><br>
    <button type="submit">Add</button>
</form>