@extends('frontend.student-dashboard.layouts.master')

@section('dashboard-contents')
    <div class="ce-invite">
        <div class="ce-invite__hero">
            <div class="ce-invite__text">
                <h2 class="ce-invite__title">{{ __('Invite a Friend, Enjoy Free Lessons!') }}</h2>
                <p class="ce-invite__lead">
                    {{ __('Introduce your friends to this unique opportunity, and earn too! When you invite a friend to our platform, you can earn between 1 and 3 free lessons depending on the package they choose.') }}
                </p>

                <div class="ce-invite__code" role="group" aria-label="{{ __('Referral Code') }}">
                    <span class="ce-invite__code-value" id="ce-ref-code">{{ $referralCode ?: '-' }}</span>
                    <button type="button" class="ce-invite__code-btn" data-copy="code">{{ __('Copy Code') }}</button>
                </div>
            </div>

            <div class="ce-invite__art" aria-hidden="true">
                <div class="ce-invite__blob ce-invite__blob--a"></div>
                <div class="ce-invite__blob ce-invite__blob--b"></div>
                <div class="ce-invite__phone">
                    <div class="ce-invite__phone-top"></div>
                    <div class="ce-invite__phone-screen">
                        <div class="ce-invite__bubble"></div>
                        <div class="ce-invite__bubble ce-invite__bubble--two"></div>
                        <div class="ce-invite__bubble ce-invite__bubble--three"></div>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="ce-invite__how">{{ __('How It Works?') }}</h3>
        <div class="ce-invite__steps">
            <div class="ce-step">
                <div class="ce-step__num ce-step__num--one">1</div>
                <div class="ce-step__text">{{ __('Copy your referral code and share it with your friend.') }}</div>
            </div>
            <div class="ce-step">
                <div class="ce-step__num ce-step__num--two">2</div>
                <div class="ce-step__text">{{ __('Your friend should enter this code in the \"Referral Code\" field during registration.') }}</div>
            </div>
            <div class="ce-step">
                <div class="ce-step__num ce-step__num--three">3</div>
                <div class="ce-step__text">{{ __('Earn 1 to 3 free lessons depending on the package your friend chooses!') }}</div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .ce-invite{display:flex;flex-direction:column;gap:18px;}
        .ce-invite__hero{
            background:#fff;
            border:1px solid #eef2f7;
            border-radius:22px;
            box-shadow:0 18px 46px rgba(0,0,0,0.10);
            padding:26px;
            display:grid;
            grid-template-columns:minmax(0, 1.2fr) minmax(0, .8fr);
            gap:18px;
            align-items:center;
        }
        .ce-invite__title{margin:0 0 10px;font-weight:1000;color:#111827;font-size:44px;line-height:1.05;letter-spacing:-.03em;}
        .ce-invite__lead{margin:0 0 14px;color:#475569;font-weight:800;max-width:640px;}

        .ce-invite__code{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:8px;}
        .ce-invite__code-value{
            font-weight:1000;
            letter-spacing:.08em;
            font-size:22px;
            color:#0f172a;
            background:#f8fafc;
            border:1px solid #e2e8f0;
            border-radius:14px;
            padding:10px 14px;
            min-width:160px;
            text-align:center;
        }
        .ce-invite__code-btn{
            border-radius:14px;
            padding:10px 14px;
            font-weight:1000;
            border:2px solid #f6a105;
            background:#fff;
            color:#f6a105;
        }
        .ce-invite__code-btn:hover{background:#fff7e6;}

        .ce-invite__art{position:relative;height:320px;display:grid;place-items:center;}
        .ce-invite__blob{position:absolute;border-radius:999px;filter:blur(0.2px);}
        .ce-invite__blob--a{width:260px;height:260px;background:radial-gradient(circle at 30% 30%, rgba(246,161,5,.35), transparent 62%);left:10px;top:10px;}
        .ce-invite__blob--b{width:280px;height:280px;background:radial-gradient(circle at 60% 40%, rgba(14,92,147,.28), transparent 62%);right:-10px;bottom:-10px;}

        .ce-invite__phone{width:220px;height:280px;border-radius:26px;background:#0f172a;box-shadow:0 30px 80px rgba(15,23,42,.35);position:relative;padding:10px;transform:rotate(-6deg);}
        .ce-invite__phone-top{height:18px;border-radius:18px;background:rgba(255,255,255,.08);margin:4px 18px 10px;}
        .ce-invite__phone-screen{height:calc(100% - 42px);border-radius:20px;background:linear-gradient(180deg,#f8fafc,#e2e8f0);position:relative;overflow:hidden;padding:14px;display:grid;gap:10px;}
        .ce-invite__bubble{height:46px;border-radius:18px;background:#fff;border:1px solid rgba(15,23,42,.08);box-shadow:0 12px 26px rgba(0,0,0,.08);}
        .ce-invite__bubble--two{width:86%;justify-self:end;background:#fff7e6;border-color:rgba(246,161,5,.25);}
        .ce-invite__bubble--three{width:72%;background:#e8f2fb;border-color:rgba(14,92,147,.2);}

        .ce-invite__how{margin:4px 0 0;font-weight:1000;color:#111827;text-align:center;}
        .ce-invite__steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;}
        .ce-step{background:#fff;border:1px solid #eef2f7;border-radius:18px;padding:18px;box-shadow:0 12px 30px rgba(0,0,0,0.06);display:flex;gap:14px;align-items:flex-start;}
        .ce-step__num{width:44px;height:44px;border-radius:14px;color:#fff;font-weight:1000;display:grid;place-items:center;flex:0 0 auto;}
        .ce-step__num--one{background:#f6a105;}
        .ce-step__num--two{background:#ef4444;}
        .ce-step__num--three{background:#06b6d4;}
        .ce-step__text{font-weight:900;color:#0f172a;line-height:1.35;}

        @media (max-width: 991.98px){
            .ce-invite__hero{grid-template-columns:1fr;}
            .ce-invite__title{font-size:34px;}
            .ce-invite__art{height:260px;}
            .ce-invite__steps{grid-template-columns:1fr;}
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function(){
            const copyText = async (text) => {
                const value = String(text || '');
                if (!value) return false;
                try {
                    await navigator.clipboard.writeText(value);
                    return true;
                } catch (e) {
                    const tmp = document.createElement('textarea');
                    tmp.value = value;
                    tmp.setAttribute('readonly', '');
                    tmp.style.position = 'absolute';
                    tmp.style.left = '-9999px';
                    document.body.appendChild(tmp);
                    tmp.select();
                    try {
                        document.execCommand('copy');
                        document.body.removeChild(tmp);
                        return true;
                    } catch (e2) {
                        document.body.removeChild(tmp);
                        return false;
                    }
                }
            };

            const buttons = document.querySelectorAll('[data-copy]');
            buttons.forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const kind = btn.getAttribute('data-copy');
                    const code = document.getElementById('ce-ref-code')?.textContent?.trim() || '';
                    const text = kind === 'link' ? '' : code;
                    const ok = await copyText(text);
                    const prev = btn.textContent;
                    btn.textContent = ok ? @json(__('Copied')) : @json(__('Copy failed'));
                    setTimeout(() => { btn.textContent = prev; }, 1400);
                });
            });
        })();
    </script>
@endpush
