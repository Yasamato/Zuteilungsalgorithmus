
public class Schueler {
    /**
     * Speichert ID des Schuelers
     */
    private int id;
    /**
     * Nachname des Schuelers
     */
    private String name;
    /**
     * Klasse des Schuelers
     */
    private String klasse;
    /**
     * Vorname des Schuelers
     */
    private String vorname;
    /**
     * Liste der Wuensche des Schuelers
     */
    Projekt[] wuensche;
    /**
     * Aktuell zugeteiles Projekt
     */
    private Projekt zugeteilt;
    /**
     * Klassenstufe des Schuelers
     */
    private int intKlasse;

    /**
     * Initialisiert alle Attribute
     *
     * @param id        ID
     * @param name      Nachname
     * @param vorname   Vorname
     * @param klasse    Klasse
     * @param wuensche  Wuensche
     * @param intKlasse Klassenstufe
     */
    public Schueler(int id, String name, String vorname, String klasse, Projekt[] wuensche, int intKlasse) {
        this.id = id;
        this.name = name;
        this.vorname = vorname;
        this.klasse = klasse;
        this.wuensche = wuensche;
        this.intKlasse = intKlasse;
    }

    /**
     * @return Attribut vorname
     */
    public String getVorname() {
        return this.vorname;
    }

    /**
     * @return Attribut name
     */
    public String getName() {
        return this.name;
    }

    /**
     * @return Attribut intKlasse; Klassenstufe
     */
    public int getIntKlasse() {
        return this.intKlasse;
    }

    /**
     * @return Attribut Klasse
     */
    public String getKlasse() {
        return this.klasse;
    }

    /**
     * Ermittelt Position eines Projektes innerhalb der Wunschliste eines Schuelers.
     * Ist das Projekt nicht in der Wunschliste, so gibt die Methode als Resultat -1 zurueck.
     *
     * @param p Projekt, das getestet werden soll
     * @return Position in der Wunschliste
     */
    public int getWahlPosition(Projekt p) {
        for (int i = 0; i < wuensche.length; i++) {
            if (wuensche[i].equals(p)) {
                return i + 1;
            }
        }
        return -1;
    }

    /**
     * @return Anzahl der Projektwuensche
     */
    public int getAnzahlGewaehlterProjekte() {
        return wuensche.length;
    }

    /**
     * @return Attribut ID
     */
    public int getId() {
        return this.id;
    }

    /**
     * @return Attribut zugeteiltes Projekt
     */
    public Projekt getZugeteiltesProjekt() {
        return zugeteilt;
    }

    /**
     * Ermittelt, ob zugeteiltes Projekt ungleich null
     *
     * @return Wahrheitswert, ob zugeteiltes Projekt ungleich null
     */
    public boolean hatZugeteiltesProjekt() {
        return (zugeteilt != null);
    }

    /**
     * Schreibt den Schueler aus seinem momentan zugeteilten Projekt aus.
     * Dabei sorgt die Methode auch dafuer, dass der Schueler aus der Teilnehmerliste des Projektes verschwindet
     */
    public void schreibeAusProjektAus() {
        assert (this.hatZugeteiltesProjekt());
        this.zugeteilt.removeTeilnehmer(this);
        this.zugeteilt = null;
    }

    /**
     * Teilt dem Schueler ein Projekt zu
     *
     * @param p Projekt, zu welchem der Schueler zugeteilt werden soll
     */
    public void teileProjektZu(Projekt p) {
        assert (!p.istVoll());
        p.addTeilnehmer(this);
        this.zugeteilt = p;
    }

    /**
     * Ermittelt ob zwei Schuelerobjekte identisch sind. Zwei Schuelerobjekte sind identisch, wenn die ID die gleich ist
     *
     * @param o Anderer Schueler
     * @return Wahrheitswert, ob anderer Schueler identisch ist
     */
    @Override
    public boolean equals(Object o) {
        if (o instanceof Schueler) {
            Schueler other = (Schueler) o;
            return other.id == this.id;
        }
        return false;
    }

    /**
     * Ermittelt, ob die n-te Wahl/ der n-te Wunsch in der Projektwunschliste noch einen freien Platz hat
     *
     * @param n n-te Wahl in der Wunschliste, mit n=1 fuer die 1.Wahl
     * @return True, wenn n-tes Projekt noch nicht voll ist
     */
    public boolean wahlFrei(int n) {
        assert (n <= wuensche.length);
        return !wuensche[n - 1].istVoll();
    }

    /**
     * @param n n-te Wahl in der Wunschliste, mit n=1 fuer die 1.Wahl
     * @return Projekt, welches an n-ter Stelle gewaehlt wurde
     */
    public Projekt getWahl(int n) {
        assert (n <= wuensche.length);
        return wuensche[n - 1];
    }

    /**
     * @return String, der den Schueler eindeutig beschreibt
     */
    @Override
    public String toString() {
        String s = "";
        s += this.id + "\n";
        for (int i = 0; i < this.wuensche.length; i++) {
            s += "Wunsch: " + (i + 1) + ": " + this.wuensche[i].getId() + "\n";
        }
        return s;
    }
}
