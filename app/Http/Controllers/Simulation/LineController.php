<?php 

namespace App\Http\Controllers\Simulation;

use DB;
use Exception;
use View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Node;

class LineController extends Controller {

    function getLines() {
        return view('simulation.lines', [
            'lines' => Line::all()
        ]);
    }

    function getNodes() {
        $nodes = Line::all();

        return view('simulation.nodes', [
            'nodes' => $nodes
        ]);
    }
}