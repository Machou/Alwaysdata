<?php
require_once '../config/wow_config.php';

$idMascotte = !empty($_GET['id_mascotte']) ? secu($_GET['id_mascotte']) : null;

if(mascotteExiste($pdo, $idMascotte))
{
	if(!empty($idMascotte))
	{
		if(mascotteMaj($pdo, $apiClient, $idMascotte))
		{
			setFlash('success', 'Mise à jour de la mascotte');

			header('Location: /mascotte/'.$idMascotte.'-mascotte');
			exit;
		}

		elseif(!empty($resMascotte['nom_mascotte']))
		{
			require_once 'a_body.php';

			$abilities_1_id					= !empty($resMascotte['abilities_1_id']) ? secu($resMascotte['abilities_1_id']) : null;
			$abilities_1_slot				= isset($resMascotte['abilities_1_slot']) ? (int) $resMascotte['abilities_1_slot'] : null;
			$abilities_1_required_level		= !empty($resMascotte['abilities_1_required_level']) ? secu($resMascotte['abilities_1_required_level']) : null;
			$abilities_1_file_data_id		= !empty($resMascotte['abilities_1_file_data_id']) ? secu($resMascotte['abilities_1_file_data_id']) : null;

			$abilities_2_id					= !empty($resMascotte['abilities_2_id']) ? secu($resMascotte['abilities_2_id']) : null;
			$abilities_2_slot				= isset($resMascotte['abilities_2_slot']) ? (int) $resMascotte['abilities_2_slot'] : null;
			$abilities_2_required_level		= !empty($resMascotte['abilities_2_required_level']) ? secu($resMascotte['abilities_2_required_level']) : null;
			$abilities_2_file_data_id		= !empty($resMascotte['abilities_2_file_data_id']) ? secu($resMascotte['abilities_2_file_data_id']) : null;

			$abilities_3_id					= !empty($resMascotte['abilities_3_id']) ? secu($resMascotte['abilities_3_id']) : null;
			$abilities_3_slot				= isset($resMascotte['abilities_3_slot']) ? (int) $resMascotte['abilities_3_slot'] : null;
			$abilities_3_required_level		= !empty($resMascotte['abilities_3_required_level']) ? secu($resMascotte['abilities_3_required_level']) : null;
			$abilities_3_file_data_id		= !empty($resMascotte['abilities_3_file_data_id']) ? secu($resMascotte['abilities_3_file_data_id']) : null;

			$abilities_4_id					= !empty($resMascotte['abilities_4_id']) ? secu($resMascotte['abilities_4_id']) : null;
			$abilities_4_slot				= isset($resMascotte['abilities_4_slot']) ? (int) $resMascotte['abilities_4_slot'] : null;
			$abilities_4_required_level		= !empty($resMascotte['abilities_4_required_level']) ? secu($resMascotte['abilities_4_required_level']) : null;
			$abilities_4_file_data_id		= !empty($resMascotte['abilities_4_file_data_id']) ? secu($resMascotte['abilities_4_file_data_id']) : null;

			$abilities_5_id					= !empty($resMascotte['abilities_5_id']) ? secu($resMascotte['abilities_5_id']) : null;
			$abilities_5_slot				= isset($resMascotte['abilities_5_slot']) ? (int) $resMascotte['abilities_5_slot'] : null;
			$abilities_5_required_level		= !empty($resMascotte['abilities_5_required_level']) ? secu($resMascotte['abilities_5_required_level']) : null;
			$abilities_5_file_data_id		= !empty($resMascotte['abilities_5_file_data_id']) ? secu($resMascotte['abilities_5_file_data_id']) : null;

			$abilities_6_id					= !empty($resMascotte['abilities_6_id']) ? secu($resMascotte['abilities_6_id']) : null;
			$abilities_6_slot				= isset($resMascotte['abilities_6_slot']) ? (int) $resMascotte['abilities_6_slot'] : null;
			$abilities_6_required_level		= !empty($resMascotte['abilities_6_required_level']) ? secu($resMascotte['abilities_6_required_level']) : null;
			$abilities_6_file_data_id		= !empty($resMascotte['abilities_6_file_data_id']) ? secu($resMascotte['abilities_6_file_data_id']) : null;

			$is_capturable 					= isset($resMascotte['is_capturable']) ? secu($resMascotte['is_capturable']) : null;
			$is_tradable					= isset($resMascotte['is_tradable']) ? secu($resMascotte['is_tradable']) : null;
			$is_battlepet					= isset($resMascotte['is_battlepet']) ? secu($resMascotte['is_battlepet']) : null;
			$is_alliance_only				= isset($resMascotte['is_alliance_only']) ? secu($resMascotte['is_alliance_only']) : null;
			$is_horde_only 					= isset($resMascotte['is_horde_only']) ? secu($resMascotte['is_horde_only']) : null;
			$creature_id					= !empty($resMascotte['creature_id']) ? secu($resMascotte['creature_id']) : null;
			$is_random_creature_display		= isset($resMascotte['is_random_creature_display']) ? secu($resMascotte['is_random_creature_display']) : null;
			$media_id						= !empty($resMascotte['media_id']) ? secu($resMascotte['media_id']) : null;

			$nom_mascotte_en 				= !empty($resMascotte['nom_mascotte_en']) ? secuChars($resMascotte['nom_mascotte_en']) : null;
			$nom_mascotte_slug				= !empty($resMascotte['nom_mascotte_slug']) ? htmlspecialchars($resMascotte['nom_mascotte_slug'], ENT_COMPAT, 'UTF-8') : null;
			$nom_mascotte_slug_en			= !empty($resMascotte['nom_mascotte_slug_en']) ? htmlspecialchars($resMascotte['nom_mascotte_slug_en'], ENT_COMPAT, 'UTF-8') : null;

			$battle_pet_type_id				= !empty($resMascotte['battle_pet_type_id']) ? secu($resMascotte['battle_pet_type_id']) : null;
			$battle_pet_type_type			= !empty($resMascotte['battle_pet_type_type']) ? secuChars($resMascotte['battle_pet_type_type']) : null;
			$battle_pet_type_name			= !empty($resMascotte['battle_pet_type_name']) ? secuChars($resMascotte['battle_pet_type_name']) : null;
			$battle_pet_type_name_en		= !empty($resMascotte['battle_pet_type_name_en']) ? secuChars($resMascotte['battle_pet_type_name_en']) : null;

			$description_mascotte			= !empty($resMascotte['description_mascotte']) ? secuChars($resMascotte['description_mascotte']) : null;
			$description_mascotte_en 		= !empty($resMascotte['description_mascotte_en']) ? secuChars($resMascotte['description_mascotte_en']) : null;

			$abilities_1_name				= !empty($resMascotte['abilities_1_name']) ? secuChars($resMascotte['abilities_1_name']) : null;
			$abilities_1_name_en 			= !empty($resMascotte['abilities_1_name_en']) ? secuChars($resMascotte['abilities_1_name_en']) : null;
			$abilities_1_media				= !empty($resMascotte['abilities_1_media']) ? secuChars($resMascotte['abilities_1_media']) : null;

			$abilities_2_name				= !empty($resMascotte['abilities_2_name']) ? secuChars($resMascotte['abilities_2_name']) : null;
			$abilities_2_name_en 			= !empty($resMascotte['abilities_2_name_en']) ? secuChars($resMascotte['abilities_2_name_en']) : null;
			$abilities_2_media				= !empty($resMascotte['abilities_2_media']) ? secuChars($resMascotte['abilities_2_media']) : null;

			$abilities_3_name				= !empty($resMascotte['abilities_3_name']) ? secuChars($resMascotte['abilities_3_name']) : null;
			$abilities_3_name_en 			= !empty($resMascotte['abilities_3_name_en']) ? secuChars($resMascotte['abilities_3_name_en']) : null;
			$abilities_3_media				= !empty($resMascotte['abilities_3_media']) ? secuChars($resMascotte['abilities_3_media']) : null;

			$abilities_4_name				= !empty($resMascotte['abilities_4_name']) ? secuChars($resMascotte['abilities_4_name']) : null;
			$abilities_4_name_en 			= !empty($resMascotte['abilities_4_name_en']) ? secuChars($resMascotte['abilities_4_name_en']) : null;
			$abilities_4_media				= !empty($resMascotte['abilities_4_media']) ? secuChars($resMascotte['abilities_4_media']) : null;

			$abilities_5_name				= !empty($resMascotte['abilities_5_name']) ? secuChars($resMascotte['abilities_5_name']) : null;
			$abilities_5_name_en 			= !empty($resMascotte['abilities_5_name_en']) ? secuChars($resMascotte['abilities_5_name_en']) : null;
			$abilities_5_media				= !empty($resMascotte['abilities_5_media']) ? secuChars($resMascotte['abilities_5_media']) : null;

			$abilities_6_name				= !empty($resMascotte['abilities_6_name']) ? secuChars($resMascotte['abilities_6_name']) : null;
			$abilities_6_name_en 			= !empty($resMascotte['abilities_6_name_en']) ? secuChars($resMascotte['abilities_6_name_en']) : null;
			$abilities_6_media				= !empty($resMascotte['abilities_6_media']) ? secuChars($resMascotte['abilities_6_media']) : null;

			$source_type					= !empty($resMascotte['source_type']) ? secuChars($resMascotte['source_type']) : null;
			$source_type_name				= !empty($resMascotte['source_type_name']) ? secuChars($resMascotte['source_type_name']) : null;
			$source_type_name_en 			= !empty($resMascotte['source_type_name_en']) ? secuChars($resMascotte['source_type_name_en']) : null;

			$icon							= !empty($resMascotte['icon']) ? secuChars($resMascotte['icon']) : null;
			$icon							= imageDistante($icon) ? 'src="'.$icon.'" data-bs-toggle="tooltip" data-bs-title="Icône de '.$nomMascotte.'"' : 'src="/assets/img/mascotte-manquante.png" data-bs-toggle="tooltip" data-bs-title="Icône de '.$nomMascotte.' inconnue"';

			if(estConnecte())
			{
				try {
					$stmtMascotteUtilisateur = $pdo->prepare('SELECT 1 FROM wow_mascottes_u WHERE id = :id_mascotte AND id_utilisateur = :id_utilisateur LIMIT 1');
					$stmtMascotteUtilisateur->execute([
						'id_mascotte' => (int) $_GET['id_mascotte'],
						'id_utilisateur' => (int) $_SESSION['id_utilisateur']
					]);

					$mascotteTrouvee = $stmtMascotteUtilisateur->fetch();
				} catch (\PDOException $e) { }
			}

			echo '<div class="container mt-5">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-10">
						<div class="mascotte-details shadow-lg">
							<div class="mascotte-details-header mb-4 p-3">
								<div class="d-flex gap-3 align-items-start mb-4">
									<img class="mascotte-media img-fluid" '.$icon.' alt="Icône '.$nomMascotte.'">

									<div class="flex-grow-1 ms-3">
										<div class="mb-4">
											<h1 class="mb-1 mt-0 fs-1 text-start"><a href="/mascotte/'.$idMascotte.'-'.$nom_mascotte_slug.'">'.$nomMascotte.'</a></h1>

											<h6 class="mb-0"><span class="me-2">'.isoEmoji('gb').'</span><span>'.$nom_mascotte_en.'</span></h6>
										</div>
									</div>
								</div>

								<div>
									<h4>Informations</h4>

									<p class="text-secondary mb-0 mt-2">ID mascotte : <span class="fw-bold">'.$idMascotte.'</span> • ID créature : <span class="fw-bold">'.(!empty($creature_id) ? $creature_id : 'inconnu').'</span> • Source : <span class="fw-bold">'.(!empty($source_type_name) ? $source_type_name : 'inconnue').'</span></p>
									<div class="mascottes-details-informations">
										'.(estConnecte() ? '<span data-bs-toggle="tooltip" data-bs-title="Mascotte '.(($mascotteTrouvee !== false) ? 'dans la collection' : 'n’est pas dans la collection').'" class="text-'.(($mascotteTrouvee !== false) ? 'success' : 'danger').'">'.(($mascotteTrouvee !== false) ? 'Collectée' : 'Non Collectée').'</span>' : null).'
										<span data-bs-toggle="tooltip" data-bs-title="Mascotte utilisable en combat">'.($is_battlepet === 1 ? 'Mascotte de combat' : 'Mascotte non combat').'</span>
										<span data-bs-toggle="tooltip" data-bs-title="Mascotte échangeable">'.($is_tradable === 1 ? 'Échangeable' : 'Non Échangeable').'</span>
										<span data-bs-toggle="tooltip" data-bs-title="Type de mascotte">'.(!empty($battle_pet_type_name) ? $battle_pet_type_name : 'Type inconnu').'</span>
									</div>
									<div class="mascottes-details-informations">
										<span data-bs-toggle="tooltip" title="Fiche Wowhead.com"><a href="https://www.wowhead.com/fr/battle-pet/'.$idMascotte.'" data-wh-rename-link="false" '.$onclick.'>Wowhead <i class="fa-solid fa-up-right-from-square"></i></a></span>
										<span data-bs-toggle="tooltip" title="Fiche WarcraftPets.com">'.(!empty($resMascotte['nom_mascotte_en']) ? '<a href="https://www.warcraftpets.com/search/?q='.urlencode(htmlspecialchars($resMascotte['nom_mascotte_en'], ENT_COMPAT, 'UTF-8')).'" '.$onclick.'>WarcraftPets <i class="fa-solid fa-up-right-from-square"></i></a>' : '<span class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="Fiche WarcraftPets.com">WarcraftPets</span>').'</span>
										<span data-bs-toggle="tooltip" title="Fiche WoWDB.com">'.(!empty($nom_mascotte_en) ? '<a href="https://www.wowdb.com/npcs/'.$creature_id.'-'.$nom_mascotte_slug_en.'" '.$onclick.'>WoWDB <i class="fa-solid fa-up-right-from-square"></i></a>' : '<span class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-title="Fichier WoWDB.com">WoWDB</span>').'</span>
										'.(estAdmin() ? '<span data-bs-toggle="tooltip" data-bs-title="Mettre à jour '.$nomMascotte.'" class="border border-success"><a href="/mascotte/'.$idMascotte.'-'.$nom_mascotte_slug.'?maj" class="link-success"><i class="fa-solid fa-rotate me-1"></i> MàJ</a></span>' : null).'
										'.(estAdmin() ? '<span data-bs-toggle="tooltip" data-bs-title="Fiche Adminer de '.$nomMascotte.'" class="border border-success"><a href="https://thisip.pw/projets/adminer.php?server=mysql-blok.alwaysdata.net&username=blok&db=blok_hdv&edit=wow_mascottes&where%5Bid%5D='.$idMascotte.'" class="link-success"><i class="fa-solid fa-database me-1"></i> Adminer</a></span>' : null).'
									</div>
								</div>
							</div>

							<div class="mb-4 p-3">
								<h4 class="mascotte-details-section-titre">Description</h4>
								<p class="mb-0">'.(!empty($description_mascotte) ? $description_mascotte : 'description inconnue').'</p>
							</div>

							<div class="p-3">
								<h4 class="mascotte-details-section-titre mb-3">Capacités</h4>';

								$capacites = [];
								for($i = 1; $i <= 6; $i++)
								{
									if(!empty(${'abilities_'.$i.'_id'}) AND !empty(${'abilities_'.$i.'_name'}))
									{
										$capacites[] = '<tr>
											<td style="width: 15%;" class="text-center" data-bs-toggle="tooltip" data-bs-title="<img src=\''.${'abilities_'.$i.'_media'}.'\' style=\'height: 100px; width: 100px;\'>"><img class="mascotte-icone-abilite" src="'.${'abilities_'.$i.'_media'}.'" alt="Icône Choc"></td>
											<td><a href="https://www.wowhead.com/fr/pet-ability='.${'abilities_'.$i.'_id'}.'" class="fw-semibold text-decoration-none">'.${'abilities_'.$i.'_name'}.'</a></td>
											<td style="width: 15%;" class="text-center">'.(${'abilities_'.$i.'_slot'} === 0 ? '0' : ${'abilities_'.$i.'_slot'}).'</td>
											<td style="width: 15%;" class="text-center">'.${'abilities_'.$i.'_required_level'}.'</td>
											<td style="width: 15%;" class="text-center">'.${'abilities_'.$i.'_id'}.'</td>
										</tr>';
									}
								}

								if(!empty($capacites))
								{
									echo '<div class="table-responsive">
										<table class="table table-dark table-hover align-middle">
											<thead>
												<tr>
													<th style="width: 15%;" class="text-center" scope="col">Icône</th>
													<th scope="col">Nom</th>
													<th style="width: 15%;" class="text-center" scope="col">Slot</th>
													<th style="width: 15%;" class="text-center" scope="col">Niveau requis</th>
													<th style="width: 15%;" class="text-center" scope="col">ID</th>
												</tr>
											</thead>
											<tbody>'.implode($capacites).'</tbody>
										</table>
									</div>';
								}

								else
									echo '<p class="fw-bold">Aucune capacité connue';

							echo '</div>
						</div>
					</div>
				</div>
			</div>';

			require_once 'a_footer.php';
		}

		else
		{
			setFlash('danger', 'Mascotte introuvable');

			header('Location: /mascottes');
			exit;
		}
	}
}

else
{
	setFlash('danger', 'Mascotte introuvable');

	header('Location: /mascottes');
	exit;
}