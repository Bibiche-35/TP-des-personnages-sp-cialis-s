# TP-des-personnages-specialises

Cahier des charges
Je veux que nous ayons le choix de créer un certain type de personnage qui aura certains avantages. Il ne doit pas être possible de créer un personnage « normal » (donc il devra être impossible d'instancier la classePersonnage). Comme précédemment, la classePersonnageaura la liste des colonnes de la table en guise d'attributs.

Je vous donne une liste de personnages différents qui pourront être créés. Chaque personnage a un atout différent sous forme d'entier.

Un magicien. Il aura une nouvelle fonctionnalité : celle de lancer un sort qui aura pour effet d'endormir un personnage pendant$atout * 6heures (l'attribut$atoutreprésente la dose de magie du personnage).

Un guerrier. Lorsqu'un coup lui est porté, il devra avoir la possibilité de parer le coup en fonction de sa protection (son atout).