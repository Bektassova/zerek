<?php
include "includes/header.php";
require_once "includes/dbh.php";

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Student') {
    header("Location: login.php");
    exit();
}

$studentId = (int) $_SESSION['userId'];

// Оставляем твой оригинальный SQL запрос
$sql = "
SELECT 
    a.assignment_id, a.title, a.due_date, a.file_path, u.unit_name,
    s.submission_id, s.submission_date,
    g.mark, g.feedback
FROM assignments a
JOIN units u ON a.unit_id = u.unit_id
JOIN student_units su ON su.unit_id = u.unit_id
LEFT JOIN submissions s ON s.assignment_id = a.assignment_id AND s.student_id = ?
LEFT JOIN grades g ON g.submission_id = s.submission_id
WHERE su.student_id = ?
ORDER BY a.due_date ASC"; // Сортировка по дате создает хронологию

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $studentId, $studentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Функция для уровней (🔴🟡🟢🔵)
function getPerf($score) {
    if ($score >= 90) return ['c' => '#007bff', 'i' => '🔵', 't' => 'Distinction'];
    if ($score >= 80) return ['c' => '#198754', 'i' => '🟢', 't' => 'Merit'];
    if ($score >= 62) return ['c' => '#ffc107', 'i' => '🟡', 't' => 'Satisfactory'];
    if ($score >= 50) return ['c' => '#dc3545', 'i' => '🔴', 't' => 'Minimum Pass'];
    return ['c' => '#6c757d', 'i' => '⚪', 't' => 'Keep working'];
}
?>

<style>
    /* ИСПРАВЛЕННЫЙ POP-UP: теперь он не пропадает */
    .feedback-container { position: relative; display: inline-block; }
    .feedback-bubble {
        position: absolute;
        bottom: 125%;
        right: 0; /* Прижимаем к правому краю ячейки */
        background: #198754;
        color: white;
        padding: 15px;
        border-radius: 10px;
        width: 300px; /* Фиксированная ширина, чтобы текст не тянулся */
        z-index: 9999;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        font-size: 0.85rem;
        line-height: 1.4;
        text-align: left;
    }
    .feedback-bubble::after {
        content: "";
        position: absolute;
        top: 100%;
        right: 20px;
        border-width: 10px;
        border-style: solid;
        border-color: #198754 transparent transparent transparent;
    }
</style>

<div class="container mt-5">
    <h2 class="mb-4 fw-bold">My Assignments & Progress</h2>

    <table class="table table-hover align-middle text-center border">
        <thead class="table-dark">
            <tr>
                <th>Unit</th>
                <th>Title</th>
                <th>Due Date</th>
                <th>Brief</th>
                <th>Status</th>
                <th>Action</th>
                <th>Grade</th>
            </tr>
        </thead>
       <tbody>
    <?php 
    $totalAccumulated = 0; 
    $history = [];         
    
    while ($row = mysqli_fetch_assoc($result)): 
        $currentMark = $row['mark'];
        
        if (!is_null($currentMark)) {
            $totalAccumulated += (int)$currentMark;
            $history[] = (int)$currentMark;

            /** 
             * ЛОГИКА ЦВЕТА И ТЕКСТА 
             * Здесь мы настраиваем, чтобы 41 балл стал КРАСНЫМ 🔴
             */
            if ($totalAccumulated >= 90) {
                $pIcon = "🔵"; $pText = "Distinction";
            } elseif ($totalAccumulated >= 80) {
                $pIcon = "🟢"; $pText = "Merit";
            } elseif ($totalAccumulated >= 62) {
                $pIcon = "🟡"; $pText = "Satisfactory";
            } elseif ($totalAccumulated >= 40) { // Для твоих 41 балла
                $pIcon = "🔴"; $pText = "Minimum Pass";
            } else {
                $pIcon = "⚪"; $pText = "Keep working";
            }
            $showPerf = true;
        } else {
            $showPerf = false;
        }
    ?>
        
        <!-- СТРОКА ХРОНОЛОГИИ -->
        <?php if ($showPerf): ?>
        <tr class="table-light">
            <td colspan="7" class="text-start ps-4 py-2 border-top">
                <span class="fw-bold">
                    <?php 
                        echo implode(" + ", $history) . " = " . $totalAccumulated; 
                    ?> 
                    → "You have <?php echo $totalAccumulated; ?>/50 so far" 
                    → <?php echo $pIcon . " " . $pText; ?>. Keep going!
                </span>
            </td>
        </tr>
        <?php endif; ?>

        <tr>
            <td><?php echo htmlspecialchars($row['unit_name']); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><small><?php echo $row['due_date']; ?></small></td>
            <td>
                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-outline-danger" download>PDF</a>
            </td>
            <td>
                <?php if ($row['submission_id']): ?>
                    <span class="badge bg-success">Submitted</span>
                <?php else: ?>
                    <span class="badge bg-secondary">Pending</span>
                <?php endif; ?>
            </td>

            <!-- ACTION COLUMN -->
            <td>
                <?php if (!$row['submission_id']): ?>
                    <a href="student-submit-assignment.php?assignment_id=<?php echo $row['assignment_id']; ?>" 
                       class="btn btn-sm btn-primary px-3 shadow-sm">
                       Submit
                    </a>
                <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled>
                        <i class="fas fa-check-circle me-1"></i> Done
                    </button>
                <?php endif; ?>
            </td>

            <!-- GRADE & FEEDBACK (ТВОЙ РОДНОЙ КОД) -->
            <td>
                <?php if (!is_null($currentMark)): ?>
                    <div class="feedback-container">
                        <!-- Здесь для баджа используем твою функцию getPerf -->
                        <span class="badge p-2 mb-1" style="background-color: <?php echo getPerf($currentMark)['c']; ?>">
                            <?php echo (int)$currentMark; ?>/100
                        </span><br>
                        <button class="btn btn-link btn-sm p-0 text-success fw-bold show-fb">See feedback</button>
                        
                        <div class="feedback-bubble d-none">
                            <strong>Teacher Feedback:</strong><br>
                            <?php echo htmlspecialchars($row['feedback']); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <span class="text-muted small">Not graded</span>
                <?php endif; ?>
            </td>
            
            <!-- Лишняя колонка удалена, так как Action уже есть выше -->
        </tr>
    <?php endwhile; ?>
</tbody>
    </table>
    <a href="profile.php" class="btn btn-secondary mt-3">Back to Profile</a>
</div>

<script>
document.querySelectorAll('.show-fb').forEach(btn => {
    btn.onclick = function(e) {
        e.preventDefault();
        const bubble = this.nextElementSibling;
        // Закрываем другие, если открыты
        document.querySelectorAll('.feedback-bubble').forEach(b => { if(b !== bubble) b.classList.add('d-none'); });
        bubble.classList.toggle('d-none');
    };
});
// Закрыть при клике в любое место
document.addEventListener('click', function(e) {
    if (!e.target.closest('.feedback-container')) {
        document.querySelectorAll('.feedback-bubble').forEach(b => b.classList.add('d-none'));
    }
});
</script>