<?php // views/covoiturage/list.php (contenu uniquement) ?>

<div class="card">
  <h1>Liste des covoiturages</h1>

  <form id="filtersForm" method="get" action="<?= BASE_URL ?>/covoiturages" class="filter-group">
    <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:end;">

      <div>
        <label class="muted" for="depart">Départ</label><br>
        <select id="depart" class="input-large" name="depart" data-auto-submit="1">
          <option value="">-- Tous --</option>
          <?php foreach (($departOptions ?? []) as $opt): ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= ($depart === $opt ? 'selected' : '') ?>>
              <?= htmlspecialchars($opt) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted" for="arrivee">Arrivée</label><br>
        <select id="arrivee" class="input-large" name="arrivee" data-auto-submit="1">
          <option value="">-- Toutes --</option>
          <?php foreach (($arriveeOptions ?? []) as $opt): ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= ($arrivee === $opt ? 'selected' : '') ?>>
              <?= htmlspecialchars($opt) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted" for="chauffeur">Chauffeur</label><br>
        <select id="chauffeur" class="input-large" name="chauffeur" data-auto-submit="1">
          <option value="">-- Tous --</option>
          <?php foreach (($chauffeurOptions ?? []) as $opt): ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= (($chauffeur ?? '') === $opt ? 'selected' : '') ?>>
              <?= htmlspecialchars($opt) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted" for="placesMin">Places restantes</label><br>
        <select id="placesMin" name="placesMin" class="input-small" data-auto-submit="1">
          <option value="">-- indifférent --</option>
          <?php foreach ([1,2,3,4,5,6] as $n): ?>
            <option value="<?= $n ?>" <?= (isset($placesMin) && (int)$placesMin === $n ? 'selected' : '') ?>>
              <?= $n ?>+
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted" for="date">Date</label><br>
        <input id="date" class="input-medium" type="date" name="date" value="<?= htmlspecialchars((string)($date ?? '')) ?>" data-auto-submit="1">
      </div>

      <div>
        <label class="muted" for="prixMax">Prix max</label><br>
        <input id="prixMax" type="number" min="0" name="prixMax"
              class="input-small"
              value="<?= htmlspecialchars((string)($prixMax ?? '')) ?>"
              data-auto-submit="1">
      </div>

      <div>
        <label class="muted" for="noteMin">Note min</label><br>
        <select id="noteMin" name="noteMin" class="input-small" data-auto-submit="1">
          <option value="">-- indifférent --</option>
          <?php
            $notes = [1,2,3,4,4.5,5];
            foreach ($notes as $n):
              $val = (string)$n;
          ?>
            <option value="<?= htmlspecialchars($val) ?>" <?= (isset($noteMin) && (string)$noteMin === $val ? 'selected' : '') ?>>
              <?= htmlspecialchars($val) ?>+
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="display:flex; gap:8px; align-items:center;">
        <input id="eco" type="checkbox" name="eco" value="1" <?= ($ecoOnly ? 'checked' : '') ?> data-auto-submit="1">
        <label for="eco">Éco (électrique)</label>
      </div>

      <div>
        <button type="submit">Filtrer</button>
        <a href="<?= BASE_URL ?>/covoiturages" style="margin-left:8px;">Réinitialiser</a>
      </div>

    </div>
  </form>

  <p style="margin-top: 10px;">
    <a href="<?= BASE_URL ?>/">← Nouvelle recherche</a>
  </p>
</div>

<br>

<?php if (empty($results)): ?>
  <div class="card">
    <p><strong>Aucun covoiturage trouvé.</strong></p>
    <p class="muted">Astuce : change la date ou teste Lyon → Paris le 2026-02-01 (données de test).</p>
  </div>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>Départ</th>
        <th>Arrivée</th>
        <th>Date / Heure</th>
        <th>Chauffeur</th>
        <th>Note</th>
        <th>Places<br>restantes</th>
        <th>Prix</th>
        <th>Éco</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $row): ?>
      <?php
        $eco = isset($row['energie']) && stripos((string)$row['energie'], 'elect') !== false;
        $note = $row['chauffeur_note'] ?? null;
      ?>
      <tr>
        <td><?= htmlspecialchars((string)$row['lieu_depart']) ?></td>
        <td><?= htmlspecialchars((string)$row['lieu_arrivee']) ?></td>
        <td>
          <?= htmlspecialchars((string)$row['date_depart']) ?>
          <?= htmlspecialchars((string)$row['heure_depart']) ?>
        </td>
        <td><?= htmlspecialchars((string)($row['chauffeur_pseudo'] ?? '—')) ?></td>
        <td><?= ($note !== null ? htmlspecialchars((string)$note) : '—') ?></td>
        <td><?= htmlspecialchars((string)$row['places_restantes']) ?></td>
        <td><?= htmlspecialchars((string)$row['prix_personne']) ?> crédits</td>
        <td><?= $eco ? 'Oui' : 'Non' ?></td>
        <td>
          <a href="<?= BASE_URL ?>/covoiturage?id=<?= (int)$row['id_covoiturage'] ?>">Détail</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<script>
  (function () {
    const form = document.querySelector('#filtersForm');
    if (!form) return;
    form.querySelectorAll('[data-auto-submit="1"]').forEach(el => {
      el.addEventListener('change', () => form.submit());
    });
  })();
</script>
