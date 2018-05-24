Display Possible Route for <b>{{ $nodeFrom->name }}</b> to <b>{{ $nodeTo->name }}</b>

<br/>
<br/>
Route : @php echo count($routes) @endphp Nodes

<table>
    <thead>
        <tr>
            <th>Node Id</th>
            <th>Line Id</th>
            <th>Sequence</th>
            <th>Name</th>
            <th>Name Ref</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($routes as $node)
        <tr>
            <td>{{ $node['id'] }}</td>
            <td>{{ $node['line_id'] }}</td>
            <td>{{ $node['sequence'] }}</td>
            <td>{{ $node['name'] }}</td>
            <td>{{ $node['name_ref'] }}</td>
        </tr>
        @endforeach
    </tbody>

</table>