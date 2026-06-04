<!-- header.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title ?? 'Admin Panel'; ?></title>
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

    /* ========== CARD STYLES ========== */
    .card {
        background: #ffffff;
        padding: 28px 32px;
        margin-bottom: 28px;
        border-radius: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.03);
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
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e6edf4;
        letter-spacing: -0.2px;
    }

    .card p {
        color: #2c5a7a;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* ========== FORM STYLES ========== */
    input, select, textarea {
        width: 100%;
        padding: 12px 16px;
        margin: 8px 0;
        border: 1.5px solid #e0e7ef;
        border-radius: 16px;
        font-size: 0.9rem;
        font-family: inherit;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #004080;
        box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.1);
    }

    button {
        background: #004080;
        color: #fff;
        cursor: pointer;
        border: none;
        font-weight: 600;
        border-radius: 40px;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 6px rgba(0, 64, 128, 0.2);
        transition: all 0.2s ease;
        padding: 12px 16px;
        width: 100%;
        font-size: 0.9rem;
    }

    button:hover {
        background: #003366;
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(0, 64, 128, 0.25);
    }

    /* ========== TABLE STYLES ========== */
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

    /* ========== BUTTON STYLES ========== */
    .btn {
        padding: 8px 18px;
        border: none;
        border-radius: 40px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        letter-spacing: 0.3px;
        width: auto;
    }

    .edit {
        background: #28a745;
        color: #ffffff;
        margin-right: 6px;
    }

    .edit:hover {
        background: #1e7e34;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .delete {
        background: #dc3545;
        color: #ffffff;
    }

    .delete:hover {
        background: #bd2130;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .delete-form {
        display: inline-block;
        margin: 0;
    }

    /* ========== MESSAGES ========== */
    .success {
        color: #1e6f3f;
        background: #e6f4ea;
        padding: 14px 18px;
        border-radius: 16px;
        margin-bottom: 20px;
        border-left: 4px solid #1e6f3f;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .error {
        color: #d14545;
        background: #fff2f0;
        padding: 14px 18px;
        border-radius: 16px;
        margin-bottom: 20px;
        border-left: 4px solid #d14545;
        font-weight: 500;
        font-size: 0.85rem;
    }

    /* Field error styling */
    .field-error {
        display: block;
        color: #d14545;
        font-size: 0.7rem;
        margin-top: -5px;
        margin-bottom: 8px;
        padding-left: 12px;
    }

    /* Info note styling */
    .info-note {
        margin-top: 20px;
        padding: 15px 20px;
        background: #f0f7fe;
        border-radius: 12px;
        border-left: 4px solid #004080;
        font-size: 0.8rem;
    }

    .info-note strong {
        color: #004080;
        display: block;
        margin-bottom: 8px;
    }

    .info-note ul {
        margin-left: 20px;
        color: #2c5a7a;
    }

    .info-note li {
        margin: 5px 0;
    }

    /* ========== BACK LINK ========== */
    .back-link {
        display: inline-block;
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

    /* ========== STATS GRID ========== */
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

    /* ========== RESPONSIVE ========== */
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
            padding: 20px;
            border-radius: 20px;
        }
        .stats-grid {
            flex-direction: column;
            gap: 16px;
        }
    }

    @media (max-width: 480px) {
        .topbar div:first-child {
            font-size: 0.75rem;
        }
        .card h3 {
            font-size: 1rem;
        }
        th, td {
            padding: 10px 12px;
            font-size: 0.75rem;
        }
        .btn {
            padding: 6px 14px;
            font-size: 0.7rem;
        }
    }
    </style>
</head>
<body>