(function () {
            var searchEl = document.getElementById('student-search');
            if (searchEl) {
                searchEl.addEventListener('keyup', function () {
                    var term = this.value.toLowerCase().trim();
                    document.querySelectorAll('.student-card').forEach(function (card) {
                        var name = card.getAttribute('data-name') || '';
                        card.style.display = !term || name.indexOf(term) !== -1 ? '' : 'none';
                    });
                });
            }
        })();

        function csrfToken() {
            var m = document.querySelector('meta[name="csrf-token"]');
            return m ? m.getAttribute('content') : '';
        }

        function houseApiNameToSlug(name) {
            var map = {
                Gryffindor: 'gryffindor',
                Slytherin: 'slytherin',
                Ravenclaw: 'ravenclaw',
                Hufflepuff: 'hufflepuff'
            };
            return map[name] || String(name || '').toLowerCase().replace(/\s+/g, '');
        }

        function bumpHouseStandingsPill(houseApiName, delta) {
            var slug = houseApiNameToSlug(houseApiName);
            var el = document.getElementById(slug + '-points');
            if (!el) {
                return;
            }
            var n = parseInt(el.textContent, 10) || 0;
            el.textContent = n + (delta || 0);
        }

        function awardHouse(houseId, el) {
            console.log('House clicked:', houseId);

            if (el && el.classList) {
                el.classList.add('clicked');
                setTimeout(function () {
                    el.classList.remove('clicked');
                }, 200);
            }

            fetch(null), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    house_id: houseId,
                    amount: 1
                })
            })
                .then(function (res) {
                    return res.json().then(function (data) {
                        if (!res.ok) {
                            throw new Error((data && data.message) ? data.message : 'bad');
                        }
                        return data;
                    });
                })
                .then(function (data) {
                    console.log('Success:', data);
                    if (data.house) {
                        bumpHouseStandingsPill(data.house, data.amount || 1);
                    }
                    if (typeof window.loadRecentActivity === 'function') {
                        window.loadRecentActivity();
                    } else if (data.recent_entry && typeof window.houseHubPrependRecentActivity === 'function') {
                        window.houseHubPrependRecentActivity(data.recent_entry);
                    }
                    if (typeof window.reportShowToast === 'function') {
                        window.reportShowToast('House point added');
                    }
                })
                .catch(function (err) {
                    console.error('Error:', err);
                    if (typeof window.reportShowToast === 'function') {
                        window.reportShowToast('Unable to update house');
                    }
                });
        }

        document.querySelectorAll('.btn-commend').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var studentId = this.dataset.studentId;
                window.openCommendationModal(studentId);
            });
        });

        document.querySelectorAll('.btn-award').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var studentId = this.dataset.studentId;
                window.openAwardModal(studentId);
            });
        });

        function escapeRecentHtml(value) {
            return String(value == null ? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderRecentActivityRows(rows) {
            var wrap = document.getElementById('recent-activity');
            if (!wrap) {
                return;
            }
            if (!rows || !rows.length) {
                wrap.innerHTML = '<p class="text-muted mb-0" style="color: #94a3b8 !important;">No recent transactions.</p>';
                return;
            }
            var html = '';
            for (var i = 0; i < rows.length; i++) {
                var r = rows[i];
                var who = '';
                if (r.student_id != null && r.student_id !== '') {
                    who = String((r.first_name || '') + ' ' + (r.last_name || '')).trim();
                    if (!who) {
                        who = r.house_name || 'Student';
                    }
                } else {
                    who = r.house_name || 'House';
                }
                var amt = r.amount != null ? Number(r.amount) : 0;
                var sign = amt > 0 ? '+' : '';
                var teacher = r.teacher != null ? String(r.teacher) : '';
                var category = r.category != null ? String(r.category).trim() : '';
                html += '<div class="activity-item mb-3 pb-2 border-bottom border-secondary" style="border-color: #334155 !important;">';
                html += '<div><strong>' + sign + amt + '</strong> ' + escapeRecentHtml(who) + '</div>';
                if (teacher) {
                    html += '<div class="text-muted" style="color: #94a3b8 !important;">' + escapeRecentHtml(teacher) + '</div>';
                }
                if (category) {
                    html += '<div class="text-muted small" style="color: #94a3b8 !important;">' + escapeRecentHtml(category) + '</div>';
                }
                html += '</div>';
            }
            wrap.innerHTML = html;
        }

        function loadRecentActivity() {
            fetch(null), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (res) {
                    if (!res.ok) {
                        throw new Error('recent');
                    }
                    return res.json();
                })
                .then(function (data) {
                    renderRecentActivityRows(Array.isArray(data) ? data : []);
                })
                .catch(function () {});
        }

        window.loadRecentActivity = loadRecentActivity;

        function initRecentActivityPolling() {
            loadRecentActivity();
            setInterval(loadRecentActivity, 5000);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initRecentActivityPolling);
        } else {
            initRecentActivityPolling();
        }