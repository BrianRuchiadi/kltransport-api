<p> <i><b>Cashless</b></i> Rate for <i><b>{{ $node->name }}</b></i> for <i><b>{{ $node->line->name }}</b></i>

<br/>
<br/>
    <a href="/simulation/fares/{{ $node->id + 1 }}/cashless">Next station</a>
<br/>
@if ($incompleteRoute)
    <br/>
    Routes not yet completed
    <br/>
    <a href="/simulation/routes/generate/{{ $node->id }}"><button>Generate It!</button></a>
    <br/>
@else
    <h3>Fare edit</h3>
    <form method="POST" action="/simulation/fares/update">
        {{ csrf_field() }}
        <input type="hidden" name="node_from_id" value={{ $node->id }}>
        <input type="hidden" name="fare_type" value="cashless">
        <select name="node_to_id">
            @foreach ($fares as $fare)
                @if ($node->id == $fare->node_from_id)
                    <option value={{$fare->node_to_id}}>{{ $fare->node_to_name }}</option>
                @else
                    <option value={{$fare->node_from_id}}>{{ $fare->node_from_name }}</option>
                @endif
            @endforeach
        </select>
        Fare
        <input type="text" name="fare"/>
        <button type="submit">Submit</button>
    </form>
@endif
<br/>

<br/>
<br/>
    
@if ($routesAvailable)
    <table>
        <thead>
            <tr>
                <th width="100"> Node Id </th>
                <th width="150"> Node Name </th>
                <th width="50"> Direct </th>
                <th width="150"> Fare </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fares as $fare) 
                @if ($node->id == $fare->node_from_id)
                <tr>
                    <td>{{ $fare->node_to_id}}</td>
                    <td>{{ $fare->node_to_line_name }} - {{ $fare->node_to_name}}</td>
                    @if ($fare->node_from_line_id == $fare->node_to_line_id)
                        <td>Yes</td>
                    @else
                        <td>No</td>
                    @endif
                    <td style="text-align: right;">{{ $fare->fare }}</td>
                </tr>
                @else
                <tr>
                    <td>{{ $fare->node_from_id}}</td>
                    <td>{{ $fare->node_from_line_name }} - {{ $fare->node_from_name}}</td>
                    @if ($fare->node_from_line_id == $fare->node_to_line_id)
                        <td>Yes</td>
                    @else
                        <td>No</td>
                    @endif
                    <td style="text-align: right;">{{ $fare->fare }}</td>                    
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@else
    Routes not yet generated
    <br/>
    <a href="/simulation/routes/generate/{{ $node->id }}"><button>Generate It!</button></a>
@endif
