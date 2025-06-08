    <div class="section-body">
        <?php if ($view === 'table'): ?>
            <div class="table-responsive">
                <table class="data-table people" id="personnelTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Rank</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Medical</th>
                            <th>Superior</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($people as $person):
                            $status_class = '';
                            switch (strtolower($person['military_status'] ?? 'active')) {
                                case 'active':
                                    $status_class = 'status-active';
                                    break;
                                case 'inactive':
                                    $status_class = 'status-inactive';
                                    break;
                                default:
                                    $status_class = 'status-pending';
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($person['id']) ?></td>
                                <td>
                                    <?php
                                    $profileFile = $person['profile_image'] ?? '';
                                    $profileImagePath = '';

                                    if (!empty($profileFile) && file_exists('../uploads/profile_images/' . $profileFile)) {
                                        $profileImagePath = '../uploads/profile_images/' . $profileFile;
                                    } else if (!empty($profileFile) && file_exists('../uploads/profile_pics/' . $profileFile)) {
                                        $profileImagePath = '../uploads/profile_pics/' . $profileFile;
                                    } else if (!empty($profileFile) && file_exists('../uploads/documents/' . $profileFile)) {
                                        $profileImagePath = '../uploads/documents/' . $profileFile;
                                    } else if (!empty($profileFile) && file_exists('../uploads/' . $profileFile)) {
                                        $profileImagePath = '../uploads/' . $profileFile;
                                    }
                                    ?>

                                    <?php if (!empty($profileImagePath)) : ?>
                                        <img src="<?= $profileImagePath ?>" class="profile-img" alt="Profile Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;">
                                    <?php else : ?>
                                        <div class="profile-initials">
                                            <?= !empty($person['name']) ? strtoupper(substr(trim($person['name']), 0, 1)) : '?' ?>
                                        </div>
                                    <?php endif; ?>


                                </td>
                                <td><?= htmlspecialchars($person['name']) ?></td>
                                <td><?= htmlspecialchars($person['age']) ?></td>
                                <td><?= htmlspecialchars($person['contact'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($person['email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($person['unit_name'] ?? 'N/A') ?></td>
                                <td><span class="status <?= $status_class ?>"><?= htmlspecialchars($person['military_status'] ?? 'N/A') ?></span></td>
                                <td><?= htmlspecialchars($person['health_status_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($person['superior_name'] ?? 'None') ?></td>
                                <td>
                                    <?php if (!empty($person['document'])): ?>
                                        <a href="../uploads/documents/<?= htmlspecialchars($person['document']) ?>" target="_blank" class="document-link">
                                            <i class="fas fa-file-alt"></i> View
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $person['id'] ?>" class="action-btn btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?delete=<?= $person['id'] ?>" class="action-btn btn-delete" title="Delete" onclick="return confirm('Delete this record?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="cards-container">
                <?php foreach ($people as $person):
                    $status_class = '';
                    switch (strtolower($person['military_status'] ?? 'active')) {
                        case 'active':
                            $status_class = 'status-active';
                            break;
                        case 'inactive':
                            $status_class = 'status-inactive';
                            break;
                        default:
                            $status_class = 'status-pending';
                    }
                ?>
                    <div class="person-card">
                        <div class="card-header">
                            <div class="profile-pic">
                                <?php
                                $profileImagePath = '../uploads/profile_pics/' . ($person['profile_pics'] ?? '');
                                if (!empty($person['profile_image']) && file_exists($profileImagePath)): ?>
                                    <img src="<?= $profileImagePath ?>" alt="Profile Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%;">
                                <?php else: ?>
                                    <div class="initials">
                                        <?= !empty($person['name']) ? substr(trim($person['name']), 0, 1) : '?' ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="card-name"><?= htmlspecialchars($person['name']) ?></div>
                                <div class="card-id">ID: <?= htmlspecialchars($person['id']) ?></div>
                                <div class="card-rank"><?= htmlspecialchars($person['rank_name'] ?? 'N/A') ?></div>
                            </div>
                        </div>

                        <div class="card-details">
                            <div class="detail-item">
                                <span class="detail-label">Age:</span>
                                <span><?= htmlspecialchars($person['age']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Unit:</span>
                                <span><?= htmlspecialchars($person['unit_name'] ?? 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="status <?= $status_class ?>"><?= htmlspecialchars($person['military_status'] ?? 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Medical:</span>
                                <span><?= htmlspecialchars($person['health_status_name'] ?? 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Contact:</span>
                                <span><?= htmlspecialchars($person['contact'] ?? 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span><?= htmlspecialchars($person['email'] ?? 'N/A') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Superior:</span>
                                <span><?= htmlspecialchars($person['superior_name'] ?? 'None') ?></span>
                            </div>
                            <?php if (!empty($person['file_upload'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Document:</span>
                                    <a href="../uploads/<?= htmlspecialchars($person['file_upload']) ?>" target="_blank" class="document-link">
                                        <i class="fas fa-file-alt"></i> View
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-actions">
                            <a href="edit.php?id=<?= $person['id'] ?>" class="btn btn-sm btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="index.php?delete=<?= $person['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete this record?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.createElement('input');
            searchInput.setAttribute('type', 'text');
            searchInput.setAttribute('id', 'searchInput');
            searchInput.setAttribute('placeholder', 'Search personnel...');
            searchInput.classList.add('search-input');

            const searchContainer = document.createElement('div');
            searchContainer.classList.add('search-mini');
            searchContainer.innerHTML = '<i class="fas fa-search"></i>';
            searchContainer.appendChild(searchInput);

            const sectionHeader = document.querySelector('.section-header');
            if (sectionHeader) {
                sectionHeader.appendChild(searchContainer);
            }

            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('#personnelTable tbody tr, .cards-container .person-card');

                rows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <?php include_once '../includes/footer.php'; ?>