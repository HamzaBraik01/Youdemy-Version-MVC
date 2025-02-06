<?php
abstract class Course {
    protected $id;
    protected $title;
    protected $description;
    protected $content;
    protected $image;
    protected $type;
    protected $status;

    // Constructeur
    public function __construct($id, $title, $description, $content, $image, $type, $status) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->image = $image;
        $this->type = $type;
        $this->status = $status;
    }

    // Méthodes abstraites
    abstract public function afficheDetails();
    abstract public function afficheCourse();
    abstract public function ajouterCourse($categorie_id);
}
?>
