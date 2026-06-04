<?php
// header.php - CSS zote ziko hapa
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title ?? 'Teacher Panel'; ?></title>
    <style>
    /* ============================================
       SMART SYSTEMATIC CSS - GOVERNMENT DASHBOARD
       Rangi: #004080, #003366, #f0f4f8
       ============================================ */

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', 'Inter', system-ui, -apple-system, 'Arial', sans-serif;
        background: #f0f4f8;
        line-height: 1.5;
        margin: 0;
        position: relative;
    }

    /* Background pattern */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background: radial-gradient(circle at 70% 20%, rgba(0,64,128,0.02) 0%, rgba(0,64,128,0.03) 100%);
        pointer-events: none;
        z-index: -1;
    }

    /* ========== SIDEBAR STYLES ========== */
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #004080 0%, #003366 100%);
        color: #ffffff;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        overflow-y: auto;
        z-index: 100;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.12);
        transition: width 0.3s ease;
    }

    .sidebar h2 {
        text-align: center;
        font-size: 1.3rem;
        font-weight: 650;
        padding: 28px 20px 24px 20px;
        margin: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        letter-spacing: 0.8px;
        background: rgba(0, 0, 0, 0.1);
    }

    /* Dropdown button styling */
    .dropdown-btn {
        display: block;
        padding: 14px 24px;
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        border-left: 4px solid transparent;
        transition: all 0.25s ease;
        margin: 4px 8px;
        border-radius: 12px;
        background: none;
        border: none;
        width: calc(100% - 16px);
        text-align: left;
        cursor: pointer;
        font-family: inherit;
    }

    .dropdown-btn:hover {
        background: rgba(255, 255, 255, 0.12);
        border-left-color: #ffcd7e;
        transform: translateX(4px);
    }

    .dropdown-btn.active {
        background: rgba(255, 255, 255, 0.12);
        border-left-color: #ffcd7e;
    }

    /* Dropdown container (hidden by default) */
    .dropdown-container {
        display: none;
        padding-left: 20px;
        margin-left: 8px;
        border-left: 2px solid rgba(255, 255, 255, 0.2);
    }

    /* Regular sidebar links */
    .sidebar a {
        display: block;
        padding: 14px 24px;
        color: #ffffff;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        border-left: 4px solid transparent;
        transition: all 0.25s ease;
        margin: 4px 8px;
        border-radius: 12px;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.12);
        border-left-color: #ffcd7e;
        transform: translateX(4px);
    }

    .sidebar a.active {
        background: rgba(255, 255, 255, 0.12);
        border-left-color: #ffcd7e;
        font-weight: 600;
    }

    /* Dropdown links */
    .dropdown-container a {
        padding: 10px 20px;
        font-size: 0.85rem;
        margin: 2px 0;
    }

    /* Sidebar scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar::-webkit-scrollbar-track {
        background: #002d55;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: #ffcd7e;
        border-radius: 10px;
    }

    /* ========== TOPBAR STYLES ========== */
    .topbar {
        margin-left: 280px;
        background: #ffffff;
        padding: 16px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 99;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .topbar div:first-child {
        font-size: 1rem;
        font-weight: 600;
        color: #004080;
        letter-spacing: 0.3px;
    }

    .topbar div:first-child strong {
        font-weight: 700;
        color: #003366;
    }

    .topbar img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .topbar img:hover {
        transform: scale(1.05);
    }

    /* ========== MAIN CONTENT STYLES ========== */
    .content {
        margin-left: 280px;
        padding: 32px 36px;
        min-height: 100vh;
        background: #f0f4f8;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 28px;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #003366;
        margin-bottom: 8px;
        letter-spacing: -0.3px;
    }

    .page-header p {
        color: #5a7a9a;
        font-size: 0.9rem;
    }

    /* ========== CARD STYLES ========== */
    .card {
        background: #ffffff;
        padding: 32px;
        margin-bottom: 28px;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 64, 128, 0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.12);
    }

    .card h3 {
        color: #004080;
        font-size: 1.25rem;
        font-weight: 650;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e6edf4;
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card h3 i {
        font-size: 1.3rem;
    }

    /* ========== FORM STYLES ========== */
    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #003366;
        margin-bottom: 8px;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    .form-group label .required {
        color: #dc3545;
        margin-left: 4px;
    }

    input[type="text"], 
    input[type="email"], 
    input[type="password"],
    input[type="number"],
    select, 
    textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e0e7ef;
        border-radius: 16px;
        font-size: 0.9rem;
        font-family: inherit;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    input[type="file"] {
        width: 100%;
        padding: 12px 16px;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        font-size: 0.9rem;
        font-family: inherit;
        transition: all 0.2s ease;
        background: #fafcff;
        cursor: pointer;
    }

    input[type="file"]:hover {
        border-color: #004080;
        background: #f0f7fe;
    }

    input:focus, 
    select:focus, 
    textarea:focus,
    input[type="file"]:focus {
        outline: none;
        border-color: #004080;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.1);
    }

    /* Button Styles */
    .btn-primary {
        background: #004080;
        color: #fff;
        cursor: pointer;
        border: none;
        font-weight: 600;
        border-radius: 40px;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 6px rgba(0, 64, 128, 0.2);
        transition: all 0.2s ease;
        padding: 14px 28px;
        width: auto;
        min-width: 180px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary:hover {
        background: #003366;
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(0, 64, 128, 0.25);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-secondary {
        background: #6c757d;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    /* Message Styles */
    .message {
        padding: 16px 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 4px solid;
    }

    .message.success {
        background: #e6f4ea;
        color: #1e6f3f;
        border-left-color: #1e6f3f;
    }

    .message.error {
        background: #fff2f0;
        color: #d14545;
        border-left-color: #d14545;
    }

    .message.info {
        background: #e8f0fe;
        color: #004080;
        border-left-color: #004080;
    }

    /* Error List */
    .error-list {
        margin-top: 16px;
        padding: 16px 20px;
        background: #fff8f0;
        border-radius: 12px;
        border-left: 4px solid #ff9800;
    }

    .error-list h4 {
        color: #e65100;
        font-size: 0.85rem;
        margin-bottom: 12px;
        font-weight: 700;
    }

    .error-list ul {
        margin-left: 20px;
        color: #bf360c;
        font-size: 0.8rem;
    }

    .error-list li {
        margin: 6px 0;
    }

    /* Info Note */
    .info-note {
        margin-top: 24px;
        padding: 20px;
        background: #f0f7fe;
        border-radius: 16px;
        border-left: 4px solid #004080;
    }

    .info-note h4 {
        color: #004080;
        font-size: 0.9rem;
        margin-bottom: 12px;
        font-weight: 700;
    }

    .info-note ul {
        margin-left: 20px;
        color: #2c5a7a;
        font-size: 0.8rem;
    }

    .info-note li {
        margin: 6px 0;
    }

    .info-note .file-format {
        background: #ffffff;
        padding: 12px;
        border-radius: 10px;
        margin-top: 12px;
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        color: #003366;
        border: 1px solid #cde0f0;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
        border-radius: 16px;
        overflow: hidden;
    }

    th, td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #e9edf2;
    }

    th {
        background: #004080;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    td {
        color: #2c3e50;
        font-size: 0.85rem;
    }

    tr:hover td {
        background: #f8fafd;
    }

    /* Action Buttons */
    .btn-edit, .btn-delete {
        padding: 6px 14px;
        border: none;
        border-radius: 40px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 0.7rem;
        font-weight: 600;
        transition: all 0.2s ease;
        letter-spacing: 0.3px;
        width: auto;
        margin: 0 4px;
    }

    .btn-edit {
        background: #28a745;
        color: #ffffff;
    }

    .btn-edit:hover {
        background: #1e7e34;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-delete {
        background: #dc3545;
        color: #ffffff;
    }

    .btn-delete:hover {
        background: #bd2130;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .stat-card {
        background: #f8fafd;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 20px 24px;
        flex: 1;
        min-width: 160px;
        text-align: center;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border-color: #00408020;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #004080;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #527a9b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-top: 8px;
    }

    /* Back Link */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 20px;
        color: #004080;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .back-link:hover {
        text-decoration: underline;
        transform: translateX(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }
        .sidebar h2 {
            font-size: 0.7rem;
            padding: 20px 8px;
            writing-mode: vertical-rl;
            text-orientation: mixed;
        }
        .sidebar a, .dropdown-btn {
            text-align: center;
            padding: 12px 0;
            font-size: 0.7rem;
            margin: 2px 4px;
            border-radius: 10px;
        }
        .dropdown-container {
            padding-left: 0;
            margin-left: 0;
            border-left: none;
        }
        .topbar, .content {
            margin-left: 80px;
        }
        .topbar {
            padding: 12px 20px;
        }
        .content {
            padding: 20px;
        }
        .card {
            padding: 24px;
        }
        .btn-primary {
            width: 100%;
        }
        .stats-grid {
            flex-direction: column;
            gap: 16px;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 {
            font-size: 1.25rem;
        }
        .card h3 {
            font-size: 1rem;
        }
        .form-group label {
            font-size: 0.8rem;
        }
        .info-note ul, .info-note .file-format {
            font-size: 0.7rem;
        }
        th, td {
            padding: 10px 12px;
            font-size: 0.75rem;
        }
    }
    </style>
</head>
<body>