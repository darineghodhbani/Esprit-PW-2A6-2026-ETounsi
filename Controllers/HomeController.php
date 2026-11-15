<?php
// Contrôleur du Front Office : il choisit quelle vue afficher.
class HomeController
{
    // Affiche une vue avec son titre et le nom de la page (pour le lien "active" du menu)
    private function render($view, $pageTitle, $currentPage)
    {
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    public function home()        { $this->render('home', 'E-Tounsi - Accueil', 'home'); }
    public function about()       { $this->render('about', 'E-Tounsi - À propos', 'about'); }
    public function courses()     { $this->render('courses', 'E-Tounsi - Services', 'courses'); }
    public function team()        { $this->render('team', 'E-Tounsi - Notre équipe', 'team'); }
    public function testimonial() { $this->render('testimonial', 'E-Tounsi - Témoignages', 'testimonial'); }
    public function contact()     { $this->render('contact', 'E-Tounsi - Contact', 'contact'); }
    public function notFound()    { $this->render('404', 'E-Tounsi - Page introuvable', '404'); }
}
