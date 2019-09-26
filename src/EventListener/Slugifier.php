<?php
namespace App\EventListener;
/*
 Documentation officielle Subscriber / listenner Doctrine : https://symfony.com/doc/current/doctrine/event_listeners_subscribers.html
*/
use App\Entity\Movie;
use App\Entity\Product;
use App\Entity\Tag;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Utils\Slugger;
class Slugifier {
    private $slugger;
    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }
    /*
     Pour chaque event déclaré dans services.yml je dois avoir une fonction qu meme nom
     si declenchement en preupdate alors il me faux dans cette classe une fonction preupdate
     PrePersit = new
     PreUpdate = edit
    
     $args contient
     l'entité associée
     l'objet en cours d'enregistrement / modification / suppression
     l'entity manager
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        //recupere l'objet a enregistrer
        $entity = $args->getObject();
        //avant d'appliquer le slug , je verifie que l'objet qui va etre enregistré est bien un Post avant d'effectuer la modification
        //$entity->setCreatedAt(new DateTime()); //sera executé pour toutes les entité par exemple
        if (!$entity instanceof Post) {
            return;
        }
        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug);
       
    }
    
    
    public function preUpdate(LifecycleEventArgs $args)
    {
        //recupere l'objet a enregistrer
        $entity = $args->getObject();

        //avant d'appliquer le slug , je verifie que l'objet qui va etre enregistré est bien un Post avant d'effectuer la modification
        if (!$entity instanceof Post) {
            return;
        }

        $slug = $this->slugger->slugify($entity->getTitle());
        $entity->setSlug($slug);
       
    }
}