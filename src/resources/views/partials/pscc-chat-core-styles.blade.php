{{-- 取引チャット・ダイレクトメッセージ等で共通利用（transactions/show のコアUI） --}}
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Yu Gothic', sans-serif;
            background-color: #F8FAFC;
            color: #333333;
            line-height: 1.5;
        }

        .pscc-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ヘッダー */
        .pscc-header {
            background: white;
            border-bottom: 1px solid #E5E7EB;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .pscc-header-content {
            max-width: 72rem;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pscc-back-button {
            padding: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            border-radius: 0.5rem;
            transition: background 0.2s;
        }

        .pscc-back-button:hover {
            background: #F3F4F6;
        }

        .pscc-back-icon {
            width: 1.25rem;
            height: 1.25rem;
            color: #4B5563;
        }

        .pscc-skill-image {
            width: 4rem;
            height: 3rem;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .pscc-header-info {
            flex: 1;
            min-width: 0;
        }

        .pscc-skill-title {
            font-size: 1.125rem;
            font-weight: bold;
            color: #111827;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pscc-header-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.875rem;
            color: #4B5563;
        }

        .pscc-meta-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .pscc-meta-icon {
            width: 1rem;
            height: 1rem;
        }

        .pscc-status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .pscc-status-delivered {
            background: #FFEDD5;
            color: #C2410C;
        }

        .pscc-status-progress {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .pscc-status-completed {
            background: #D1FAE5;
            color: #047857;
        }

        /* チャットエリア */
        .pscc-chat-area {
            flex: 1;
            overflow-y: auto;
            background-color: #F8FAFC;
        }

        .pscc-chat-content {
            max-width: 64rem;
            margin: 0 auto;
            padding: 1.5rem 1rem;
        }

        .pscc-messages {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* システムメッセージ */
        .pscc-message-system {
            display: flex;
            justify-content: center;
        }

        .pscc-system-bubble {
            background: #F3F4F6;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            max-width: 28rem;
        }

        .pscc-system-text {
            font-size: 0.875rem;
            color: #374151;
            text-align: center;
        }

        .pscc-system-time {
            font-size: 0.75rem;
            color: #6B7280;
            text-align: center;
            margin-top: 0.25rem;
        }

        /* ユーザーメッセージ（カード形式・左揃え） */
        .pscc-message {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .pscc-message-card {
            flex: 1;
            background: #FFFFFF;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            padding: 1rem 1.25rem;
            max-width: 100%;
        }

        .pscc-message-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.625rem;
        }

        .pscc-message-card-header-left {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            min-width: 0;
        }

        .pscc-avatar {
            width: 2.5rem;
            height: 2.5rem;
            min-width: 2.5rem;
            border-radius: 9999px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .pscc-avatar-initial {
            width: 2.5rem;
            height: 2.5rem;
            min-width: 2.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .pscc-message-meta {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            min-width: 0;
        }

        .pscc-sender-name {
            font-size: 0.9375rem;
            font-weight: 500;
            color: #333333;
        }

        .pscc-message-time-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            color: #9B9B9B;
        }

        .pscc-message-time {
            color: #9B9B9B;
        }

        .pscc-read-status {
            color: #9B9B9B;
            font-size: 0.8125rem;
        }

        .pscc-message-options {
            padding: 0.25rem;
            color: #9B9B9B;
            background: none;
            border: none;
            cursor: pointer;
            border-radius: 0.25rem;
            flex-shrink: 0;
        }

        .pscc-message-options:hover {
            color: #4A4A4A;
            background: #F3F4F6;
        }

        .pscc-message-body {
            color: #333333;
            font-size: 0.9375rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .pscc-message-reactions {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
            font-size: 1.125rem;
        }

        /* アクションボタンエリア */
        .pscc-action-area {
            background: white;
            border-top: 1px solid #E5E7EB;
            padding: 0.75rem 1rem;
        }

        .pscc-action-content {
            max-width: 64rem;
            margin: 0 auto;
            display: flex;
            gap: 0.75rem;
        }

        .pscc-approve-button,
        .pscc-deliver-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .pscc-approve-button:hover,
        .pscc-deliver-button:hover {
            background: #059669;
        }

        .pscc-button-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* メッセージ入力エリア */
        .pscc-input-area {
            background: white;
            border-top: 1px solid #E5E7EB;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .pscc-input-content {
            max-width: 64rem;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .pscc-input-avatar {
            width: 2.5rem;
            height: 2.5rem;
            min-width: 2.5rem;
            border-radius: 9999px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .pscc-input-avatar-initial {
            width: 2.5rem;
            height: 2.5rem;
            min-width: 2.5rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
            background: #E5E7EB;
            color: #374151;
        }

        .pscc-attach-button {
            padding: 0.75rem;
            color: #4B5563;
            background: none;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.2s;
        }

        .pscc-attach-button:hover {
            background: #F3F4F6;
        }

        .pscc-attach-icon {
            width: 1.25rem;
            height: 1.25rem;
        }

        .pscc-input-field {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            font-size: 1rem;
            outline: none;
            transition: all 0.2s;
        }

        .pscc-input-field:focus {
            border-color: #F97316;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
        }

        .pscc-input-field.pscc-input-error {
            border-color: #DC2626;
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.2);
        }

        .pscc-field-error {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #DC2626;
        }

        .pscc-send-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #F97316;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .pscc-send-button:hover {
            background: #EA580C;
        }

        .pscc-send-button:disabled {
            background: #D1D5DB;
            cursor: not-allowed;
        }
</style>
