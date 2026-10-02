@once
    @push('head')
        <style>
            .mail-simple {
                --mail-ink: #1f1b16;
                --mail-muted: #6d665c;
                --mail-line: #ded6c9;
                --mail-bg: #f8f5ef;
                --mail-card: #ffffff;
                --mail-gold: #c8922a;
                --mail-gold-soft: #fff5dd;
                --mail-green: #13734c;
                --mail-blue: #2f6f9f;
                --mail-red: #b42318;
                color: var(--mail-ink);
            }
            .mail-simple * { box-sizing: border-box; }
            .mail-simple input[type="radio"] {
                display: inline-block !important;
                width: auto !important;
                min-height: 0 !important;
                padding: 0 !important;
                accent-color: var(--mail-gold);
            }
            .mail-topbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                border: 1px solid var(--mail-line);
                border-radius: 12px;
                background: var(--mail-card);
                padding: 1rem;
                box-shadow: 0 8px 22px rgba(31, 27, 22, .06);
            }
            .mail-topbar h1 {
                margin: 0;
                font-size: clamp(1.8rem, 3vw, 2.45rem) !important;
                line-height: 1 !important;
            }
            .mail-topbar p {
                margin: .35rem 0 0;
                color: var(--mail-muted);
                font-size: .92rem;
            }
            .mail-actions {
                display: flex;
                flex-wrap: wrap;
                gap: .6rem;
            }
            .mail-layout {
                display: grid;
                grid-template-columns: 15.5rem minmax(0, 1fr);
                gap: 1rem;
                align-items: start;
            }
            .mail-panel {
                border: 1px solid var(--mail-line);
                border-radius: 12px;
                background: var(--mail-card);
                box-shadow: 0 8px 22px rgba(31, 27, 22, .05);
            }
            .mail-sidebar {
                position: sticky;
                top: 9rem;
                padding: .85rem;
            }
            .mail-account {
                border: 1px solid var(--mail-line);
                border-radius: 10px;
                background: #fbfaf6;
                padding: .8rem;
            }
            .mail-account span {
                display: block;
                color: var(--mail-muted);
                font-size: .68rem;
                font-weight: 900;
                letter-spacing: .12em;
                text-transform: uppercase;
            }
            .mail-account strong {
                display: block;
                margin-top: .3rem;
                overflow-wrap: anywhere;
                font-size: .86rem;
            }
            .mail-folder-list {
                display: grid;
                gap: .25rem;
                margin-top: .8rem;
            }
            .mail-folder {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: center;
                gap: .6rem;
                min-height: 2.5rem;
                border-radius: 9px;
                padding: .55rem .65rem;
                color: var(--mail-muted);
                font-size: .86rem;
                font-weight: 800;
                transition: background .15s ease, color .15s ease, transform .15s ease;
            }
            .mail-folder:hover {
                background: #faf3e3;
                color: #8a601d;
            }
            .mail-folder.is-active {
                background: var(--mail-gold-soft);
                color: #8a601d;
            }
            .mail-count {
                min-width: 1.55rem;
                border-radius: 999px;
                background: #eee9df;
                padding: .14rem .4rem;
                text-align: center;
                color: var(--mail-muted);
                font-size: .68rem;
                font-weight: 900;
            }
            .mail-folder.is-active .mail-count,
            .mail-count.is-hot {
                background: var(--mail-gold);
                color: white;
            }
            .mail-side-links {
                display: grid;
                gap: .45rem;
                margin-top: .8rem;
            }
            .mail-side-links a {
                display: flex;
                min-height: 2.35rem;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--mail-line);
                border-radius: 9px;
                background: white;
                color: var(--mail-muted);
                font-size: .78rem;
                font-weight: 900;
                text-transform: uppercase;
            }
            .mail-main {
                padding: 1rem;
            }
            .mail-main-head {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-end;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: .8rem;
            }
            .mail-main-head h2 {
                margin: 0;
                font-size: 1.65rem !important;
            }
            .mail-main-head p {
                margin: .25rem 0 0;
                color: var(--mail-muted);
                font-size: .86rem;
            }
            .mail-filter {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 12rem auto;
                gap: .65rem;
                margin-bottom: .85rem;
            }
            .mail-filter .vant-input {
                min-height: 2.75rem !important;
                border-radius: 9px !important;
            }
            .mail-bulk-toolbar {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin: 14px 0;
                padding: 12px;
                border: 1px solid rgba(212,160,67,.22);
                border-radius: 12px;
                background: #fffaf0;
            }

            .mail-select-all {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                color: #3f3a34;
                font-size: 13px;
                font-weight: 800;
            }

            .mail-select-all input,
            .mail-check input {
                width: 16px;
                height: 16px;
                accent-color: #c8922a;
            }

            .mail-bulk-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .vant-button-danger {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 38px;
                border: 1px solid #fecaca;
                border-radius: 999px;
                background: #fff1f2;
                color: #9f1239;
                padding: 9px 15px;
                font-size: 12px;
                font-weight: 900;
                cursor: pointer;
            }

            .mail-row-wrap {
                display: grid;
                grid-template-columns: 34px minmax(0, 1fr) auto;
                gap: 10px;
                align-items: stretch;
            }

            .mail-check {
                display: grid;
                place-items: center;
                min-height: 78px;
                border: 1px solid #ece4d8;
                border-radius: 12px;
                background: #fff;
            }

            .mail-row-trash {
                display: flex;
                align-items: stretch;
            }

            .mail-row-trash button {
                min-width: 58px;
                border: 1px solid #fee2e2;
                border-radius: 12px;
                background: #fff7f7;
                color: #991b1b;
                font-size: 12px;
                font-weight: 900;
                cursor: pointer;
            }

            .mail-row-trash button:hover,
            .vant-button-danger:hover {
                border-color: #fca5a5;
                background: #ffe4e6;
            }

            .mail-list {
                display: grid;
                gap: .5rem;
            }
            .mail-row {
                display: grid;
                grid-template-columns: 2.55rem minmax(0, 1fr) auto;
                gap: .75rem;
                align-items: center;
                border: 1px solid #e8e1d5;
                border-radius: 10px;
                background: white;
                padding: .75rem;
                color: var(--mail-ink);
                transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
            }
            .mail-row:hover {
                transform: translateY(-1px);
                border-color: rgba(200, 146, 42, .45);
                box-shadow: 0 10px 22px rgba(31, 27, 22, .08);
            }
            .mail-row.is-unread {
                background: #fffbf2;
                border-color: rgba(200, 146, 42, .38);
            }
            .mail-avatar {
                display: grid;
                width: 2.55rem;
                height: 2.55rem;
                place-items: center;
                border-radius: 10px;
                background: #f0e4cb;
                color: #8a601d;
                font-size: .75rem;
                font-weight: 950;
            }
            .mail-row.is-unread .mail-avatar {
                background: var(--mail-gold);
                color: white;
            }
            .mail-row-main {
                min-width: 0;
            }
            .mail-row-top {
                display: flex;
                flex-wrap: wrap;
                gap: .4rem;
                align-items: center;
            }
            .mail-contact,
            .mail-subject {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .mail-contact {
                color: var(--mail-ink);
                font-size: .9rem;
                font-weight: 900;
            }
            .mail-subject {
                margin-top: .25rem;
                color: #2d281f;
                font-size: .94rem;
                font-weight: 800;
            }
            .mail-preview {
                margin-top: .18rem;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                color: var(--mail-muted);
                font-size: .8rem;
            }
            .mail-meta {
                display: grid;
                justify-items: end;
                gap: .35rem;
                color: var(--mail-muted);
                font-size: .74rem;
                white-space: nowrap;
            }
            .mail-status {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: .26rem .5rem;
                font-size: .64rem;
                font-weight: 900;
                line-height: 1;
                text-transform: uppercase;
            }
            .mail-status-open { background: #eaf4ef; color: var(--mail-green); }
            .mail-status-sent { background: #eaf1f8; color: var(--mail-blue); }
            .mail-status-failed { background: #fdecec; color: var(--mail-red); }
            .mail-status-draft { background: #fff4d9; color: #8a601d; }
            .mail-status-default { background: #f1eee9; color: var(--mail-muted); }
            .mail-empty {
                border: 1px dashed var(--mail-line);
                border-radius: 10px;
                background: #fffdf8;
                padding: 1.5rem;
                text-align: center;
            }
            .mail-reader-layout,
            .mail-compose-layout {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 19rem;
                gap: 1rem;
                align-items: start;
            }
            .mail-reader,
            .mail-composer,
            .mail-action-panel {
                padding: 1rem;
            }
            .mail-reader h2,
            .mail-composer h2,
            .mail-action-panel h2 {
                margin-top: 0;
            }
            .mail-reader-head {
                display: flex;
                gap: .8rem;
                align-items: flex-start;
                border-bottom: 1px solid var(--mail-line);
                padding-bottom: 1rem;
            }
            .mail-reader-head h2 {
                margin: .2rem 0 .4rem;
                font-size: clamp(1.6rem, 3vw, 2.35rem) !important;
                line-height: 1.08 !important;
            }
            .mail-pills {
                display: flex;
                flex-wrap: wrap;
                gap: .4rem;
            }
            .mail-body {
                margin-top: 1rem;
                border-radius: 10px;
                background: #fffdf8;
                padding: 1rem;
                color: #2d281f;
                font-size: .95rem;
                line-height: 1.7;
            }
            .mail-action-grid {
                display: grid;
                gap: .55rem;
            }
            .mail-action-grid .vant-button,
            .mail-action-grid .vant-button-outline {
                width: 100%;
            }
            .mail-reply-box,
            .mail-attach-zone {
                margin-top: 1rem;
                border: 1px solid var(--mail-line);
                border-radius: 10px;
                background: white;
                padding: .9rem;
            }
            .mail-compose-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .8rem;
            }
            .mail-composer {
                display: grid;
                gap: .9rem;
            }
            .mail-editor-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: .4rem;
                border: 1px solid var(--mail-line);
                border-bottom: 0;
                border-radius: 10px 10px 0 0;
                background: #faf6ed;
                padding: .45rem;
            }
            .mail-tool {
                min-height: 2.1rem !important;
                border-radius: 8px !important;
                padding: .35rem .65rem !important;
                font-size: .76rem !important;
            }
            .mail-editor {
                min-height: 18rem;
                border: 1px solid var(--mail-line);
                border-radius: 0 0 10px 10px;
                background: white;
                padding: .9rem;
                color: #2d281f;
                outline: none;
                line-height: 1.65;
            }
            .mail-editor:focus {
                border-color: rgba(200, 146, 42, .55);
                box-shadow: 0 0 0 3px rgba(200, 146, 42, .12);
            }
            .mail-note {
                color: var(--mail-muted);
                font-size: .84rem;
                line-height: 1.65;
            }
            .mail-simple,
            .mail-topbar,
            .mail-layout,
            .mail-panel,
            .mail-main,
            .mail-list,
            .mail-row,
            .mail-row-main,
            .mail-reader-layout,
            .mail-reader,
            .mail-reader-head,
            .mail-body,
            .mail-compose-layout,
            .mail-composer {
                min-width: 0;
            }
            .mail-topbar > div:first-child,
            .mail-main-head > div:first-child,
            .mail-reader-head > div {
                min-width: 0;
            }
            .mail-topbar h1,
            .mail-topbar p,
            .mail-main-head h2,
            .mail-main-head p,
            .mail-note {
                overflow-wrap: anywhere;
                word-break: break-word;
            }
            .mail-row {
                max-width: 100%;
                overflow: hidden;
            }
            .mail-row-top {
                min-width: 0;
            }
            .mail-row-top .mail-contact {
                flex: 1 1 auto;
                min-width: 0;
            }
            .mail-contact,
            .mail-subject,
            .mail-preview {
                display: block;
                max-width: 100%;
                min-width: 0;
            }
            .mail-meta {
                min-width: max-content;
            }
            .mail-body {
                max-width: 100%;
                overflow: auto;
                overflow-wrap: anywhere;
                word-break: break-word;
            }
            .mail-body *,
            .mail-reader-head *,
            .mail-pills * {
                max-width: 100%;
                overflow-wrap: anywhere;
                word-break: break-word;
            }
            .mail-body pre {
                white-space: pre-wrap;
            }
            .mail-body table {
                display: block;
                max-width: 100%;
                overflow-x: auto;
            }
            .mail-live-indicator {
                display: inline-flex;
                align-items: center;
                gap: .45rem;
                min-height: 2rem;
                border: 1px solid #e8e1d5;
                border-radius: 999px;
                background: #fffdf8;
                padding: .35rem .65rem;
                color: var(--mail-muted);
                font-size: .72rem;
                font-weight: 900;
                white-space: nowrap;
            }
            .mail-live-indicator.is-error {
                color: var(--mail-red);
            }
            .mail-live-dot {
                width: .52rem;
                height: .52rem;
                border-radius: 999px;
                background: var(--mail-green);
                box-shadow: 0 0 0 0 rgba(19, 115, 76, .28);
                animation: mailLivePulse 1.8s ease-out infinite;
            }
            .mail-live-indicator.is-error .mail-live-dot {
                background: var(--mail-red);
                animation: none;
            }
            @keyframes mailLivePulse {
                0% { box-shadow: 0 0 0 0 rgba(19, 115, 76, .28); }
                70% { box-shadow: 0 0 0 .45rem rgba(19, 115, 76, 0); }
                100% { box-shadow: 0 0 0 0 rgba(19, 115, 76, 0); }
            }
            @media (prefers-reduced-motion: reduce) {
                .mail-live-dot {
                    animation: none;
                }
            }
            @media (max-width: 680px) {
                .mail-meta {
                    min-width: 0;
                    max-width: 100%;
                }
            }
            @media (max-width: 980px) {
                .mail-layout,
                .mail-reader-layout,
                .mail-compose-layout {
                    grid-template-columns: 1fr;
                }
                .mail-sidebar {
                    position: static;
                }
            }
            @media (max-width: 680px) {
                .mail-filter,
                .mail-compose-grid {
                    grid-template-columns: 1fr;
                }
                .mail-row {
                    grid-template-columns: 2.4rem minmax(0, 1fr);
                }
                .mail-meta {
                    grid-column: 2;
                    justify-items: start;
                    display: flex;
                    flex-wrap: wrap;
                }
            }
        </style>
    @endpush
@endonce