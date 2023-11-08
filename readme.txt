crée un systeme d'affectation des tâches pour des utilisateurs,
user : 
	nom
	prenom
	age
	niveau [deb, inter, expert]
	******* mdp min 5 caract not working

tâche:
	type [migration, installation, portabilité]
	difficulté
	nom
	code (format xx-xxxx-xx)
	date_debut
	durée = 1h
1- crée un api pour l enregistrement des users (signup)
2- crée un systeme d'auth (jwt) (signin)
3- crée une commande sf qui récupere la liste des tâches depuis un fichier excel + les API :ajouter / modifier /supprimer 
4- calculer la difficulté de la tache: max(type, code)
	(
		migration = 1
		installation = 2
		porta = 4
	)
	(
		code commancant par 'ot' => 2
		code commancant par 'as' => 3
		code contient 'rsta' => 1
		code contient 'ftth' => 4
	)
5- crée un api qui affecte des tâches à la liste des utilisateur:
	1-type deb = peut prendre 2 tâches par jours qui ont une difficulté =< 1 (1 tâche max par heure)
	2-type inter = peut prendre 4 tâches par jours qui ont une difficulté =< 3 (1 tâche max par heure)
	3-type expert = peut prendre 8 tâches par jours (2 tâches max par heure)
