<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Award Points | HouseHub</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --gryffindor: #ae0001; --slytherin: #2a623d;
            --ravenclaw: #222f5b; --hufflepuff: #f0c75e;
            --bg: #f4f7f6; --text: #333;
        }
        body { font-family: system-ui, -apple-system, sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        
        /* Header & Search */
        header { margin-bottom: 25px; }
        .search-container { position: sticky; top: 10px; z-index: 100; margin-bottom: 20px; }
        #studentSearch { 
            width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #ddd;
            font-size: 1.1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); outline: none;
        }

        /* House Quick Buttons */
        .house-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 30px; }
        .house-btn { 
            border: none; border-radius: 12px; padding: 20px; color: white; font-weight: bold;
            font-size: 1rem; cursor: pointer; transition: transform 0.1s; text-align: center;
        }
        .house-btn:active { transform: scale(0.95); }
        .bg-gryffindor { background: var(--gryffindor); }
        .bg-slytherin { background: var(--slytherin); }
        .bg-ravenclaw { background: var(--ravenclaw); color: white; }
        .bg-hufflepuff { background: var(--hufflepuff); color: #111; }

        /* Student Cards */
        .student-list { display: grid; gap: 15px; }
        .student-card { 
            background: white; border-radius: 15px; padding: 15px; 
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .student-info h3 { margin: 0; font-size: 1.2rem; }
        .student-info p { margin: 4px 0 0; color: #666; font-size: 0.9rem; }
        .points-badge { 
            display: inline-block; padding: 4px 10px; border-radius: 20px; 
            font-weight: bold; margin-top: 8px; font-size: 0.85rem; 
        }

        /* Action Buttons */
        .actions { display: flex; gap: 8px; }
        .btn { 
            border: none; border-radius: 10px; padding: 12px 18px; 
            font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .btn-plus { color: white; font-size: 1.2rem; min-width: 60px; }
        .btn-star { background: #eee; font-size: 1.2rem; }
        .btn-trophy { background: #eee; font-size: 1.2rem; }

        /* Modal Styles */
        #awardModal { 
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; 
            width: 100%; height: 100%; background: rgba(0,0,0,0.6); 
        }
        .modal-content { 
            background: white; margin: 10% auto; padding: 25px; border-radius: 20px; 
            width: 90%; max-width: 450px; position: relative; 
        }
        .modal-content h2 { margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { 
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box;
        }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
        .btn-submit { background: #333; color: white; padding: 12px 25px; }
        .btn-cancel { background: #ddd; padding: 12px 25px; }

        /* Success Toast */
        #toast { 
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
            background: #333; color: white; padding: 12px 25px; border-radius: 50px;
            display: none; z-index: 2000;
        }
    </style>
</head>
<body>

<div class="container">
    <header>
        <h1>Award Points</h1>
        <div class="search-container">
            <input type="text" id="studentSearch" placeholder="Search students by name or house..." onkeyup="filterStudents()">
        </div>
    </header>

    <div class="house-grid">
        @foreach(['Gryffindor' => 'bg-gryffindor', 'Slytherin' => 'bg-slytherin', 'Ravenclaw' => 'bg-ravenclaw', 'Hufflepuff' => 'bg-hufflepuff'] as $name => $class)
            <form action="{{ route('points.store') }}" method="POST">
                @csrf
                <input type="hidden" name="house_name" value="{{ $name }}">
                <input type="hidden" name="amount" value="1">
                <button type="submit" class="house-btn {{ $class }}" style="width: 100%">
                    {{ strtoupper($name) }}<br>+1 House Point
                </button>
            </form>
        @endforeach
    </div>

    <div class="student-list" id="studentList">
        @foreach($students as $student)
            <div class="student-card" data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}" data-house="{{ strtolower($student->house_name) }}">
                <div class="student-info">
                    <h3>{{ $student->first_name }} {{ $student->last_name }}</h3>
                    <p>Year {{ $student->year_level }} • {{ $student->house_name }}</p>
                    <span class="points-badge" style="background: {{ $student->colour_hex }}22; color: {{ $student->colour_hex }}">
                        {{ $student->house_points }} Points
                    </span>
                </div>
                
                <div class="actions">
                    <form action="{{ route('points.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="amount" value="1">
                        <button type="submit" class="btn btn-plus" style="background: {{ $student->colour_hex }}">
                            +1
                        </button>
                    </form>

                    <form action="{{ route('commendations.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <button type="submit" class="btn btn-star">⭐</button>
                    </form>

                    <button class="btn btn-trophy" onclick="openAwardModal({{ $student->id }}, '{{ $student->first_name }}')">🏆</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div id="awardModal">
    <div class="modal-content">
        <h2 id="modalTitle">Issue Award</h2>
        <form id="awardForm">
            <input type="hidden" id="modal_student_id">
            <div class="form-group">
                <label>Award Name</label>
                <input type="text" id="award_name" required placeholder="e.g. Science Excellence">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="award_description" rows="3" required placeholder="Describe why they earned this..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-submit">Save Award</button>
            </div>
        </form>
    </div>
</div>

<div id="toast">Success! Action recorded.</div>

<script>
    // 1. Live Search Logic
    function filterStudents() {
        const query = document.getElementById('studentSearch').value.toLowerCase();
        const cards = document.querySelectorAll('.student-card');
        
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const house = card.getAttribute('data-house');
            if (name.includes(query) || house.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // 2. Modal Management
    const modal = document.getElementById('awardModal');
    
    function openAwardModal(id, name) {
        document.getElementById('modal_student_id').value = id;
        document.getElementById('modalTitle').innerText = `Award for ${name}`;
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
        document.getElementById('awardForm').reset();
    }

    // 3. AJAX Submission for Awards
    document.getElementById('awardForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = {
            student_id: document.getElementById('modal_student_id').value,
            award_name: document.getElementById('award_name').value,
            award_description: document.getElementById('award_description').value
        };

        fetch("{{ route('award.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => {
            showToast(res.message || "Award issued successfully!");
            closeModal();
        })
        .catch(err => {
            alert("Error saving award. Please try again.");
        });
    });

    // 4. Utilities
    function showToast(msg) {
        const toast = document.getElementById('toast');
        toast.innerText = msg;
        toast.style.display = 'block';
        setTimeout(() => { toast.style.display = 'none'; }, 3000);
    }

    // Close modal if clicking outside content
    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    }
</script>

</body>
</html>