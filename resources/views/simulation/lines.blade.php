<table>
    <thead>
        <tr>
            <th width="100">Id</th>
            <th width="100">Type</th>
            <th width="100">Reference</th>
            <th width="100">Name</th>
            <th width="100">Icon</th>
            <th width="100">Total Station</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($lines as $line)
            <tr>
                <td style="text-align: center"> {{ $line->id }} </td>
                <td> {{ strtoupper($line->type) }} </td>
                <td> {{ $line->reference }} </td>
                <td> {{ $line->name }} </td>
                <td> {{ $line->icon }} </td>
                <td style="text-align: center"> {{ $line->total_station }} </td>
            </tr>
        @endforeach
    </tbody>
</table>