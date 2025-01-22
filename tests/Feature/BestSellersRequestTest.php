<?php

namespace Tests\Feature;

use App\Http\Requests\BestSellersRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class BestSellersRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_fails()
    {
        $request = new Request([
            'isbn' => ['invalid_isbn'],
            'offset' => 15,
        ]);

        $request->setMethod('GET');

        $bestSellersRequest = new BestSellersRequest();
        $bestSellersRequest->merge($request->all());

        $validator = Validator::make($bestSellersRequest->all(), $bestSellersRequest->rules());

        $this->assertTrue($validator->fails());
        $this->assertEquals([
            0 => "offset",
            1 => "isbn.0"
        ], array_keys($validator->errors()->toArray()));
    }

    public function test_validation_passes()
    {
        $request = new Request([
            'isbn' => ['1234567890'],
            'offset' => 40,
        ]);

        $request->setMethod('GET');

        $bestSellersRequest = new BestSellersRequest();
        $bestSellersRequest->merge($request->all());

        $validator = Validator::make($bestSellersRequest->all(), $bestSellersRequest->rules());

        $this->assertTrue($validator->passes());
    }
}
