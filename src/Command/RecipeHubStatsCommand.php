<?php

namespace App\Command;

use App\Repository\RecetteRepository;
use App\Repository\CategorieRecetteRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:recipehub:stats',
    description: 'Affiche les statistiques de la plateforme de recettes',
)]
class RecipeHubStatsCommand extends Command
{
    public function __construct(
        private RecetteRepository $recetteRepository,
        private CategorieRecetteRepository $categorieRepository,
        private IngredientRepository $ingredientRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('detail', null, InputOption::VALUE_NONE, 'Affiche le détail par catégorie')
            ->addOption('top', null, InputOption::VALUE_REQUIRED, 'Affiche le top N des recettes les plus longues');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('📊 STATISTIQUES RECETTEHUB');
        
        $conn = $this->entityManager->getConnection();
        
        // 1. Nombre total de recettes
        $totalRecettes = count($this->recetteRepository->findAll());
        $totalPubliees = count($this->recetteRepository->findBy(['publiee' => true]));
        $totalBrouillons = $totalRecettes - $totalPubliees;
        
        $io->section('📚 Recettes');
        $io->writeln(sprintf('   Total: %d', $totalRecettes));
        $io->writeln(sprintf('   Publiées: %d', $totalPubliees));
        $io->writeln(sprintf('   Brouillons: %d', $totalBrouillons));
        
        // 2. Nombre d'ingrédients total
        $totalIngredients = count($this->ingredientRepository->findAll());
        $io->section('🥄 Ingrédients');
        $io->writeln(sprintf('   Total: %d', $totalIngredients));
        
        // 3. Temps de préparation moyen
        $tempsMoyen = $this->calculerTempsMoyenPreparation();
        $io->section('⏱️ Temps moyen');
        $io->writeln(sprintf('   Préparation: %d minutes', $tempsMoyen));
        
        // 4. Détail par catégorie (option --detail)
        if ($input->getOption('detail')) {
            $io->section('📂 Répartition par catégorie');
            
            $sql = 'SELECT c.nom, COUNT(r.id) as count 
                    FROM recette r 
                    LEFT JOIN categorie_recette c ON r.categorie_id = c.id 
                    GROUP BY c.nom';
            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery();
            $recettesParCategorie = $result->fetchAllAssociative();
            
            $rows = [];
            foreach ($recettesParCategorie as $row) {
                $rows[] = [$row['nom'] ?? 'Sans catégorie', $row['count']];
            }
            $io->table(['Catégorie', 'Nombre de recettes'], $rows);
        }
        
        // 5. Répartition par difficulté
        $io->section('⭐ Répartition par difficulté');
        
        $sql = 'SELECT difficulte, COUNT(*) as count FROM recette GROUP BY difficulte';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $recettesParDifficulte = $result->fetchAllAssociative();
        
        $diffData = ['facile' => 0, 'moyen' => 0, 'difficile' => 0];
        foreach ($recettesParDifficulte as $row) {
            $diffData[$row['difficulte']] = $row['count'];
        }
        
        $io->writeln(sprintf('   Facile: %d', $diffData['facile']));
        $io->writeln(sprintf('   Moyen: %d', $diffData['moyen']));
        $io->writeln(sprintf('   Difficile: %d', $diffData['difficile']));
        
        // 6. Top N des recettes les plus longues (option --top)
        if ($input->getOption('top')) {
            $topN = (int)$input->getOption('top');
            $io->section(sprintf('🏆 Top %d des recettes les plus longues', $topN));
            
            $sql = 'SELECT r.titre, r.temps_preparation, r.temps_cuisson, u.pseudo as auteurPseudo 
                    FROM recette r 
                    LEFT JOIN user u ON r.auteur_id = u.id 
                    ORDER BY (r.temps_preparation + COALESCE(r.temps_cuisson, 0)) DESC 
                    LIMIT ' . $topN;
            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery();
            $topRecettes = $result->fetchAllAssociative();
            
            $rows = [];
            foreach ($topRecettes as $recette) {
                $tempsTotal = ($recette['temps_preparation'] ?? 0) + ($recette['temps_cuisson'] ?? 0);
                $rows[] = [
                    $recette['titre'],
                    $tempsTotal . ' min',
                    $recette['auteurPseudo'] ?? 'Inconnu'
                ];
            }
            $io->table(['Titre', 'Temps total', 'Auteur'], $rows);
        }
        
        // 7. Top 3 des auteurs les plus prolifiques
        $io->section('👨‍🍳 Top 3 des auteurs les plus prolifiques');
        
        $sql = 'SELECT u.pseudo, COUNT(r.id) as total 
                FROM recette r 
                JOIN user u ON r.auteur_id = u.id 
                GROUP BY u.id, u.pseudo 
                ORDER BY total DESC 
                LIMIT 3';
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        $topAuteurs = $result->fetchAllAssociative();
        
        $rows = [];
        foreach ($topAuteurs as $auteur) {
            $rows[] = [$auteur['pseudo'], $auteur['total']];
        }
        $io->table(['Auteur', 'Nombre de recettes'], $rows);
        
        $io->success('Statistiques affichées avec succès !');
        
        return Command::SUCCESS;
    }
    
    private function calculerTempsMoyenPreparation(): int
    {
        $recettes = $this->recetteRepository->findAll();
        if (count($recettes) === 0) {
            return 0;
        }
        
        $total = 0;
        foreach ($recettes as $recette) {
            $total += $recette->getTempsPreparation();
        }
        
        return (int)($total / count($recettes));
    }
}