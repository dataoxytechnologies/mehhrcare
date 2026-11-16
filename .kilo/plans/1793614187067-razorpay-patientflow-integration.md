# Razorpay Payment Gateway Integration for Patient Flow

## Goal

Enable patients to pay their programme bills via Razorpay directly from the patient flow board. Instead of a QR code page, clicking "Pay" should go directly to `admin/patientflow/pay?ref=billing_reference` where the Razorpay payment screen shows with the correct programme bill amount. After successful payment, update the visit/billing status accordingly.

---

## Current State Analysis

### What Already Exists

| Component | File | Status |
|-----------|------|--------|
| Admin Razorpay settings | `application/controllers/admin/Paymentsettings.php` (tab_7) | ✅ Exists |
| Patient Razorpay controller | `application/controllers/patient/payment/Razorpay.php` | ✅ Exists |
| Patient Razorpay view | `application/views/patient/payment/razorpay/razorpay.php` | ✅ Exists |
| Patient Pay controller | `application/controllers/patient/Pay.php` | ✅ Exists |
| QR code generation | `Patientflow::generate_qr_code()` in `Patientflow.php:1992` | ✅ Exists (will be removed/replaced) |
| QR code view | `application/views/admin/patientflow/qr_code.php` | ✅ Exists (will be removed/not used) |
| Programme bill generation | `Patientflow::generate_programme_bill()` in `Patientflow.php:3540` | ✅ Exists |
| Patient flow visit table columns | `programme_status`, `billing_reference`, `billing_status`, `invoice_no`, `ct_programme_id` | ✅ Already migrated |

### Current Problem

The board's "Generate Bill" button links to `admin/patientflow/pay?ref=billing_reference`, which shows a **manual payment form** (cash/cheque/etc.) — NOT Razorpay. The QR code page is unnecessary and should be removed.

### Required Behavior

1. User clicks "Pay" on the board → goes to `admin/patientflow/pay?ref=BR-KTR-261104-000001`
2. Pay page shows Razorpay payment screen with the **programme bill amount** (e.g., ₹2,000.00)
3. Patient pays via Razorpay
4. After payment, visit status updates to completed/paid
5. Token number is freed for reuse

---

## Implementation Plan

### Phase 1: Modify `generate_qr_code()` → `generate_pay_link()`

**File:** `application/controllers/admin/Patientflow.php`

Change `generate_qr_code()` to redirect directly to `admin/patientflow/pay?ref=billing_reference` instead of generating a QR code page. The method should:
- Validate visit exists
- Verify programme bill exists
- Redirect to `admin/patientflow/pay?ref={billing_reference}`

### Phase 2: Update board.php button

**File:** `application/views/admin/patientflow/board.php`

The "Generate Bill" button already exists. Ensure it points to `generate_qr_code()` which now redirects to pay page. Or add a dedicated "Pay" button next to it.

### Phase 3: Modify `Patientflow::pay()` to show Razorpay

**File:** `application/controllers/admin/Patientflow.php`

Modify `pay()` method (around line 2036) to:
- Detect if Razorpay is the active payment method
- If Razorpay is active, redirect to `patient/payment/razorpay` instead of showing manual payment form
- Pass the programme bill amount and visit_id in session
- If not Razorpay, show existing manual payment form (cash/cheque/etc.)

### Phase 4: Update Razorpay Controller for Patient Flow Payments

**File:** `application/controllers/patient/payment/Razorpay.php`

Add `patientflow` case in callback:
```php
} elseif ($payment_data['payment_for'] == "patientflow") {
    $save_record["visit_id"] = $payment_data['id'];
}
```

Also update visit's `billing_status` to 'paid' and `cash_received` to 1 after successful payment.

### Phase 5: Add `getProgrammeBillAmount()` to Model

**File:** `application/models/Patientflow_model.php`

Add a method that retrieves the payable amount for a visit's programme bill:
```php
public function getProgrammeBillAmount($visit_id) {
    // Query patient_flow_fee_invoice where invoice_type='PROGRAMME'
    // and invoice_no matches visit->invoice_no
    // Return grand_total
}
```

### Phase 6: Store Payment Data in Session

**File:** `application/controllers/patient/Pay.php`

Add logic to detect when `payment_for=patientflow` is in session and route accordingly, OR create a new entry point that sets session data then redirects to Razorpay.

Simplest approach: Add a new method `patientflow_payment($visit_id)` in `Pay.php` that:
1. Gets visit and bill amount
2. Stores session data
3. Redirects to `patient/payment/razorpay`

---

## Files to Modify

| File | Change |
|------|--------|
| `application/controllers/admin/Patientflow.php` | Modify `generate_qr_code()` to redirect to `admin/patientflow/pay?ref=...` |
| `application/controllers/patient/Pay.php` | Add `patientflow_payment()` method OR modify `index()` to detect patientflow payment |
| `application/controllers/patient/payment/Razorpay.php` | Add `patientflow` case in callback, update visit billing status |
| `application/models/Patientflow_model.php` | Add `getProgrammeBillAmount()` method |
| `application/views/admin/patientflow/board.php` | Verify "Pay" button links to correct endpoint |

---

## Files NOT Needed

- `application/views/admin/patientflow/qr_code.php` — DELETE or leave unused (QR code page not needed)
- New controllers/views — All existing files can be reused

---

## Data Flow

```
Staff clicks "Pay" on board
    ↓
Patientflow::generate_qr_code($visit_id) → redirects to admin/patientflow/pay?ref=billing_reference
    ↓
Patientflow::pay() detects Razorpay is active, redirects to patient/payment/razorpay
    ↓
Razorpay::index() shows checkout page with programme bill amount
    ↓
Patient pays via Razorpay
    ↓
Razorpay::callback() saves transaction, updates visit billing_status to 'paid'
    ↓
Patient sees success page, redirected back to board
```

---

## Validation Checklist

- [ ] Razorpay credentials configured in admin settings (`admin/paymentsettings`, tab_7)
- [ ] Board "Pay" button generates correct URL with billing reference
- [ ] Pay page redirects to Razorpay with correct programme bill amount
- [ ] Successful payment updates visit `billing_status` to 'paid'
- [ ] Payment transaction recorded in system
- [ ] Token freed for reuse if visit is completed
- [ ] Error handling for failed payments
- [ ] Error handling for missing Razorpay configuration

---

## Edge Cases

| Scenario | Handling |
|----------|----------|
| Razorpay not configured in settings | Show error message, redirect back to board |
| Visit has no programme bill | Show error, do not show pay page |
| Payment fails | Show payment failure page, allow retry |
| Patient not logged in | Redirect to patient login first |
| Duplicate payment | Check if `billing_status` is already 'paid' |
| Multiple visits for same patient | Each visit gets its own payment |

---

## Out of Scope

- Creating new payment gateways beyond Razorpay
- Refund processing via Razorpay
- Partial payments (single payment per bill assumed)
- Auto-generating programme bills (already exists via `generate_programme_bill()`)
- QR code page (`qr_code.php`) — will no longer be used

---

## Prerequisites

Before implementation:
1. Razorpay API credentials must be configured in `admin/paymentsettings` (tab_7)
2. Patient must be logged in to access `patient/pay` routes
3. Visit must have an existing programme bill (generated via `generate_programme_bill()`)

---

## Files Summary

### Files to Modify
1. `application/controllers/admin/Patientflow.php` — Modify `generate_qr_code()` to redirect to pay page
2. `application/controllers/patient/Pay.php` — Add `patientflow_payment()` method or modify existing
3. `application/controllers/patient/payment/Razorpay.php` — Add `patientflow` case in callback
4. `application/models/Patientflow_model.php` — Add `getProgrammeBillAmount()` method
5. `application/views/admin/patientflow/board.php` — Verify Pay button links correctly

### Files NOT Needed
- No new files required
- `qr_code.php` view can remain but won't be used

---
