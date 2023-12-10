<div class="card card-default card-bordered p-2 card-radious">
    <div class="card-header card-header-actions">
        <div class="card-title">
            <h4 class="text-uppercase">
                <strong>
                    Account Balances
                </strong>
            </h4>
        </div>
    </div>
    <div class="card-body">
        <ul class="card-stats-items">
            <li>
                <div class="card-stats-item">
                    <span>Cash</span>
                    <h4>{{ \App\Helpers\CodeHelper::formatPrice(__(':amount', ['amount' => $row->balance])) }}</h4>
                </div>
            </li>
            <li>
                <div class="card-stats-item">
                    <span>Credits</span>
                    <h4>{{ __(':amount', ['amount' => 0]) }}</h4>
                </div>
            </li>
        </ul>
        <hr />
        <h6>Promo Credits</h6>
        <p>Your have $10.00 Promo Credits in your account. </p>
        <p>These promotional credits are <span class="text-danger">set to expire on January 14,
                2024</span></p>
        <a href="javascript:;" class="btn btn-primary">Use Promo Credits Now</a>
        <hr />
        <h6>Earn Credits</h6>
        <p>Use your referral code below, and invite friends, colleagues and associates
            to try MyOffice. Receive $25.00 in your account every time someone signs up
            with your referral code.</p>
        <div class="referral-box">
            <p>Your Unique Code</p>
            <a href="javascript:;" class="btn btn-primary">xTjdjljsmd</a>
            <a href="javascript:;" class="btn btn-dark">Share My Code</a>
        </div>
    </div>
</div>
