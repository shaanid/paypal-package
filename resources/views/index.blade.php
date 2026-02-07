<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment | PayPal Integration Pro</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-blue: #0070ba;
            --secondary-blue: #003087;
            --accent-blue: #009cde;
            --paypal-gold: #ffc439;
            --paypal-gold-hover: #f4b41a;
            --glass-white: rgba(255, 255, 255, 0.85);
            --surface-color: #ffffff;
            --text-main: #1d1d1f;
            --text-muted: #6e6e73;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210,100%,15%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(220,100%,10%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(230,100%,15%,1) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            overflow-x: hidden;
            padding: 20px;
        }

        /* Animated Background Blobs */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(0, 112, 186, 0.2) 0%, rgba(0, 48, 135, 0.2) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: -1;
            animation: move 25s infinite alternate;
        }

        .blob-1 { top: -10%; left: -10%; }
        .blob-2 { bottom: -10%; right: -10%; animation-delay: -5s; }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 100px) scale(1.2); }
        }

        .checkout-container {
            perspective: 1000px;
            width: 100%;
            max-width: 480px;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: var(--glass-white);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .card-header {
            background: transparent;
            border: none;
            padding: 40px 40px 20px;
            text-align: center;
        }

        .brand-icon-wrapper {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            font-size: 2.5rem;
            color: var(--primary-blue);
        }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.75rem;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .card-body {
            padding: 0 40px 40px;
        }

        .order-summary-box {
            background: rgba(15, 23, 42, 0.03);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid rgba(15, 23, 42, 0.05);
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.95rem;
            color: var(--text-muted);
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(15, 23, 42, 0.1);
            font-weight: 700;
            font-size: 1.25rem;
            color: #0f172a;
        }

        .btn-pay {
            background: var(--paypal-gold);
            color: #000;
            border: none;
            padding: 18px 30px;
            border-radius: 18px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-pay:hover {
            background: var(--paypal-gold-hover);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -6px rgba(244, 180, 26, 0.3);
        }

        .btn-pay:active {
            transform: translateY(0);
        }

        .btn-pay::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: 0.5s;
        }

        .btn-pay:hover::after {
            left: 100%;
        }

        .alert {
            border-radius: 20px;
            border: none;
            padding: 16px 20px;
            font-size: 0.95rem;
            margin-bottom: 25px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .payment-security {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 25px;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .security-badge {
            background: #e2e8f0;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .loading-overlay {
            display: none;
            position: absolute;
            inset: 0;
            background: var(--paypal-gold);
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
            border-width: 0.2em;
        }
    </style>
</head>
<body>

    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="checkout-container">
        <div class="card">
            <div class="card-header">
                <div class="brand-icon-wrapper">
                    <i class="fab fa-paypal"></i>
                </div>
                <h1 class="brand-title">Secure Checkout</h1>
                <p class="text-muted small">Complete your purchase securely via PayPal</p>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fas fa-check-circle me-3 fa-lg"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-3 fa-lg"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                <div class="order-summary-box">
                    <div class="item-row">
                        <span>Premium Service Plan</span>
                        <span>$10.00</span>
                    </div>
                    <div class="item-row">
                        <span>Standard Taxes</span>
                        <span>$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Total Amount</span>
                        <span>$10.00 USD</span>
                    </div>
                </div>

                <form id="paymentForm" action="{{ route('paypal.process') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-pay" id="payButton">
                        <span class="button-text"><i class="fab fa-paypal"></i> Pay & Continue</span>
                        <div class="loading-overlay" id="loadingOverlay">
                            <div class="spinner-border text-dark" role="status"></div>
                        </div>
                    </button>
                </form>

                <div class="payment-security">
                    <i class="fas fa-shield-alt text-primary"></i>
                    <span>Bank-level security</span>
                    <span class="security-badge">PCI DSS</span>
                </div>
                
                <p class="text-center mt-4 text-muted" style="font-size: 0.75rem;">
                    By clicking "Pay & Continue", you agree to our Terms of Service.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function() {
            const btn = document.getElementById('payButton');
            const overlay = document.getElementById('loadingOverlay');
            btn.style.pointerEvents = 'none';
            overlay.style.display = 'flex';
        });
    </script>

</body>
</html>