<?php
class PersonnagesManager
{
  private $db; // Instance de PDO
  
  public function __construct($db)
  {
    $this->db = $db;
  }
  
  // Enregistrer un nouveau personnage
  public function add(Personnage $perso)
  {
    // Préparation de la requête d'insertion.
    $q = $this->db->prepare('INSERT INTO personnages_v2(nom, type) VALUES(:nom, :type)');
    // Assignation des valeurs pour le nom et le type du personnage.
    $q->bindValue(':nom', $perso->nom());
    $q->bindValue(':type', $perso->type());
    // Exécution de la requête.
    $q->execute();
    
    // Hydratation du personnage passé en paramètre avec assignation de son identifiant et des dégâts initiaux (= 0).
    $perso->hydrate([
      'id' => $this->db->lastInsertId(),
      'degats' => 0,
      'atout' => 0
    ]);
  }


  // Compter le nombre de personnages
  public function count()
  {
    // Exécute une requête COUNT() et retourne le nombre de résultats retourné.
    return $this->db->query('SELECT COUNT(*) FROM personnages_v2')->fetchColumn();
  }
  

  // supprimer un personnage
  public function delete(Personnage $perso)
  {
    // Exécute une requête de type DELETE.
    $this->db->exec('DELETE FROM personnages_v2 WHERE id = '.$perso->id());
  }
  

  //savoir si un personnage existe.
  public function exists($info)
  {
    // Si le paramètre est un entier, c'est qu'on a fourni un identifiant.
    if (is_int($info)) // On veut voir si tel personnage ayant pour id $info existe.
    {
      // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.
      return (bool) $this->db->query('SELECT COUNT(*) FROM personnages_v2 WHERE id = '.$info)->fetchColumn();
    }
    
    // Sinon c'est qu'on a passé un nom.
    // Exécution d'une requête COUNT() avec une clause WHERE, et retourne un boolean.
    $q = $this->db->prepare('SELECT COUNT(*) FROM personnages_v2 WHERE nom = :nom');
    $q->execute([':nom' => $info]);
    
    return (bool) $q->fetchColumn();
  }
  
  // sélectionner un personnage
  public function get($info)
  {
    // Si le paramètre est un entier, on veut récupérer le personnage avec son identifiant.
    if (is_int($info))
    {
      // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
      $q = $this->db->query('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE id = '.$info);
      $perso = $q->fetch(PDO::FETCH_ASSOC);
    }

    // Sinon, on veut récupérer le personnage avec son nom.
    else
    {   
      // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
      $q = $this->db->prepare('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE nom = :nom');
      $q->execute([':nom' => $info]);
    
      $perso = $q->fetch(PDO::FETCH_ASSOC);
    }
    switch ($perso['type'])
    {
      case 'guerrier': return new Guerrier($perso);
      case 'magicien': return new Magicien($perso);
      default: return null;
    }

  }
  
  // récupérer une liste de plusieurs personnages
  public function getList($nom)
  {
    // Retourne la liste des personnages dont le nom n'est pas $nom.
    $persos = [];
    
    $q = $this->db->prepare('SELECT id, nom, degats, timeEndormi, type, atout FROM personnages_v2 WHERE nom <> :nom ORDER BY nom');
    $q->execute([':nom' => $nom]);
    
    // Le résultat sera un tableau d'instances de Personnage.
    while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
    {
      switch ($donnees['type'])
      {
        case 'guerrier': $persos[] = new Guerrier($donnees); break;
        case 'magicien': $persos[] = new Magicien($donnees); break;
      }
    }
    
    return $persos;
  }
  

  // modifier un personnage
  public function update(Personnage $perso)
  {
    // Prépare une requête de type UPDATE.
    $q = $this->db->prepare('UPDATE personnages_v2 SET degats = :degats, timeEndormi = :timeEndormi, atout = :atout WHERE id = :id');
    // Assignation des valeurs à la requête.
    $q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
    $q->bindValue(':timeEndormi', $perso->timeEndormi(), PDO::PARAM_INT);
    $q->bindValue(':atout', $perso->atout(), PDO::PARAM_INT);
    $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);
    // Exécution de la requête.
    $q->execute();
  }
}