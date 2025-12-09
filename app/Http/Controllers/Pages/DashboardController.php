<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller as ParentController;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

// use RadicalLoop\Eod\Config;
// use RadicalLoop\Eod\Eod;
use Eod;

class DashboardController extends ParentController
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats'         => $this->getStats(),
            'livestocks'    => $this->getStocks(),
        ]);
    }

    public function getStats()
    {
        return Cache::remember('dashboard_stats', 60, function () {
            $totalMembers = User::count();
            $newMembersToday = User::whereDate('created_at', today())->count();
            $memberGrowth = $this->calculateGrowthPercentage(
                User::whereDate('created_at', '>=', now()->subDays(7))->count(),
                User::whereDate('created_at', '>=', now()->subDays(14))->whereDate('created_at', '<', now()->subDays(7))->count()
            );

            return [
                [
                    'title'  => 'Total Members',
                    'value'  => number_format($totalMembers),
                    'growth' => sprintf('%+.1f%%', $memberGrowth),
                ],
                [
                    'title'  => 'New Members Today',
                    'value'  => number_format($newMembersToday),
                    'growth' => sprintf('%+.1f%%', $newMembersToday > 0 ? 100 : 0),
                ],
                [
                    'title'  => 'Weekly Growth',
                    'value'  => sprintf('%+.1f%%', $memberGrowth),
                    'growth' => sprintf('%+.1f%%', $memberGrowth),
                ],
                [
                    'title'  => 'Total Sessions',
                    'value'  => number_format(rand(5000, 15000)),
                    'growth' => sprintf('%+.1f%%', rand(5, 15)),
                ],
            ];
        });
    }

    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (($current - $previous) / $previous) * 100;
    }

    public function getStocks()
    {
        $data = [];

        // https://github.com/radicalloop/eodhistoricaldata

        // $exchanges = Eod::exchange();
        // $exchange->symbol('US')->json();
        // $exchange->multipleTicker('US')->json();
        // $exchange->details('US')->json();

        $stocks = Eod::stock();
        $apple = $stocks->realTime('AAPL.US')->json();
        // $apple = $stocks->eod('AAPL.US')->json();


$dataObject = json_decode($apple);


        // dd($dataObject);

// {
//     symbol: 'AAPL',
//     name: 'Apple, Inc',
//     price: '173.25',
//     change: 0.86,
//     icon: 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg',
//     bgColor: 'gray',
// }

//   +"code": "AAPL.US"
//   +"timestamp": 1765217040
//   +"gmtoffset": 0
//   +"open": 278.13
//   +"high": 279.6693
//   +"low": 276.7
//   +"close": 277.74
//   +"volume": 17596827
//   +"previousClose": 278.78
//   +"change": -1.04
//   +"change_p": -0.3731
// }


        $data = [
            'symbol'    => $dataObject->code,
            'name'      => 'Apple',
            'price'     => $dataObject->close,
            'change'    => $dataObject->change,
            'icon'      => 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg',
            'bgColor'   => 'gray',
            'currency'  => '$',
        ];

        return $data;

        // return response()->json($data);


        // return $data;
    }


/*

{
  "stockCode": "AAPL.US",
  "timestamp": 1765215720,
  "gmtOffset": 0,
  "prices": {
    "open": 278.13,
    "high": 279.67,
    "low": 276.70,
    "close": 277.23
  },
  "volume": 16443062,
  "previousClose": 278.78,
  "change": {
    "amount": -1.55,
    "percentage": -0.56
  }
}


  {
    symbol: 'AAPL',
    name: 'Apple, Inc',
    price: '173.25',
    change: 0.86,
    icon: 'https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg',
    bgColor: 'gray',
  },

getStocks()

*/



}
