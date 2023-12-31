<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\Settings;

class UpdateAdminSpaceSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Settings::where('name', 'space_booking_buyer_fees')->update([
            'val' => '[{"name":"Host Fees","desc":"Fee charged to Host on booking.","name_ja":"","desc_ja":"","name_egy":null,"desc_egy":null,"price":"5","unit":"percent","type":"one_time"},{"name":"Guest Fees","desc":"Fee charged to Guest on booking.","name_ja":"","desc_ja":"","name_egy":null,"desc_egy":null,"price":"5","unit":"percent","type":"one_time"}]'
        ]);
    }

    /**
     * Reverse the migrations. 
     *
     * @return void
     */
    public function down()
    {
        Settings::where('name', 'space_booking_buyer_fees')->update([
            'val' => '[{"name":"Cleaning fee","desc":"One-time fee charged by host to cover the cost of cleaning their space.","name_ja":"\u30af\u30ea\u30fc\u30cb\u30f3\u30b0\u4ee3","desc_ja":"\u30b9\u30da\u30fc\u30b9\u3092\u6383\u9664\u3059\u308b\u8cbb\u7528\u3092\u30db\u30b9\u30c8\u304c\u8acb\u6c42\u3059\u308b1\u56de\u9650\u308a\u306e\u6599\u91d1\u3002","name_egy":null,"desc_egy":null,"price":"100","unit":"fixed","type":"one_time"},{"name":"Service fee","desc":"This helps us run our platform and offer services like 24\/7 support on your trip.","name_ja":"\u30b5\u30fc\u30d3\u30b9\u6599","desc_ja":"\u3053\u308c\u306b\u3088\u308a\u3001\u5f53\u793e\u306e\u30d7\u30e9\u30c3\u30c8\u30d5\u30a9\u30fc\u30e0\u3092\u5b9f\u884c\u3057\u3001\u65c5\u884c\u4e2d\u306b","name_egy":null,"desc_egy":null,"price":"200","unit":"fixed","type":"one_time"}]'
        ]);
    }
}
