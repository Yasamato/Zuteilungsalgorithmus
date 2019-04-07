import java.io.File;
import java.io.FileWriter;
import java.util.ArrayList;


public class Main {
    /**
     * Anzahl der Cores, die fertig gerechnet haben
     */
    public static int coresFinished = 0;
    /**
     * Anzahl der Cores, die parallel rechnen
     */
    public static int cores = 1;
    /**
     * Liste aller Threads, die gerade parallel rechnen
     */
    public static ArrayList<Verteilungsthread> corethreads = new ArrayList<Verteilungsthread>();

    /**
     * Start des Programmes; ruft Abfrage auf, liest Dateien ein und fuehrt Algorithmus aus.
     *
     * @param args Kommandozeilenparameter
     */
    public static void main(String[] args) {
        if (args.length != 6) {
            System.out.println("Ungültige Eingabe! Usage: ");
            System.out.println("java -jar Algorithmus.jar prozessoren durchgaenge pathToProjektCSV projektCSVBeschreibung pathToSchuelerCSV schuelerCSVBeschreibung");
            System.exit(0);
        }
        int maxDurchgaenge = -1;
        String path = null;
        String regex = null;
        String path2 = null;
        String regex2 = null;
        try {
            cores = Integer.parseInt(args[0]);
            if (cores > Runtime.getRuntime().availableProcessors()) {
                System.out.println("Angegebene Anzahl an Prozessoren ist größer als maximale verfügbare Prozessoren");
                System.exit(0);
            }
            if (cores < 1) {
                System.out.println("Anzahl der Prozessoren ist zu gering!");
                System.exit(0);
            }
            maxDurchgaenge = Integer.parseInt(args[1]);
            path = args[2];
            regex = args[3];
            path2 = args[4];
            regex2 = args[5];
        } catch (Exception e) {
            e.printStackTrace();
        }
        ProjectFileReader projectReader = new ProjectFileReader(path, regex);
        ArrayList<Projekt> projektListe = projectReader.getProjekte();
        SchuelerFileReader schuelerReader = new SchuelerFileReader(path2, regex2, projektListe);
        ArrayList<Schueler> schuelerListe = schuelerReader.getSchueler();

        //Ausfuehren des Algorithmus
        int durchgaenge = maxDurchgaenge / cores;
        for (int i = 0; i < cores; i++) {
            Verteilungsthread t = new Verteilungsthread("Thread " + i, schuelerListe, projektListe, durchgaenge);
            t.start();
            corethreads.add(t);
        }


    }

    public static void incrementCoresFinished() {
        coresFinished++;
        if (coresFinished == cores) {
            File f = new File("prozentzahl");
            if (f.exists()) {
                f.delete();
            }
            Verteilung bestVerteilung = null;
            double minScore = 10000;
            for (Verteilungsthread t : corethreads) {
                double newScore = t.bestVerteilung.getScore();
                if (newScore < minScore) {
                    minScore = newScore;
                    bestVerteilung = t.bestVerteilung;
                }
            }
            //Beste Verteilung wird ausgegeben, und in CSV-Dateien gespeichert.
            bestVerteilung.macheAusgabe(true);
            //erstellen der Ausgabe-CSV
            Writer.writeSchuelerListe(bestVerteilung.schuelerListe);
            Writer.writeProjektListe(bestVerteilung.schuelerListe);
        }
    }

    /**
     * Methode die zur Hilfe der Erstellung der Regression angefertig wurde.
     * Die Methode nimmt Datensets einer bestimmten Groesse n und verteilt sie auf  auf m Projektplätze.
     * Mit x = n/m gilt herauszufinden, wie viel Projektwuensche y jeder Schueler angeben soll.
     * Diese Methode gibt dann aus, wie viele Projektewuensche bei bestimmten X sinnvoll sind.
     * Die Regression ergab: y_inprozent = 75.4745x^4 - 223.148x^3 + 246.145x^2- 119.873x + 21.8101 , wobei die Regression nur fuer x kleiner gleich 0.6 sinnvolle Werte liefert.
     * Um die Anzahl der Wuensche zu ermitteln nimmt man y = y_inprozent* anzahl verfuegbarer projekte , wobei es sinnvoll ist y auf 4 zu setzen, wenn y kleiner 4. y muss zudem immer aufgerundet werden.
     *
     * @param schuelerListe SchuelerListe basierend auf welcher die Testsets erzeugt werden. Die Testsets geben dabei moeglichst genau das originale Testset wieder (von den Wahrscheinlichkeiten).
     * @param projektListe  Projektliste basierend auf welcher die Testsets erzeugt werden. Die Testsets geben dabei moeglichst genau das originale Testset wieder( von den Wahrscheinlichkeiten).
     */
    public static void testeRegression(ArrayList<Schueler> schuelerListe, ArrayList<Projekt> projektListe) {
        double sum = 0.0;
        //Macht 100 Testsets einer bestimmten Groesse X
        //Berechent fuer jedes Testset welche Anzahl y an Projektwuenschen sinnvoll wäre, und nimmt davon den Durschnitt.
        int durchlaeufe = 100;
        for (int i = 0; i < durchlaeufe; i++) {
            double score = 100000;
            //Dabei wird fuer jede Verteilung der Algoritmus angewandt, und zwar mehrere tausend mal, genau wie in "Realität", sodass durch das Mischen von Datensets bessere Ergebnisse vorkommen koennen.
            Verteilung bestVerteilung = null;
            System.out.println("I: " + i);
            //Erzeugt ein Testset einer bestimmten Groesse
            ErzeugeTestsets t = new ErzeugeTestsets(858, schuelerListe, projektListe);
            for (int n = 0; n < 10000; n++) {

                //Kopiert Objekte und Listen, sodass diese bei der Verteilung nicht zerstoert werden.
                ArrayList<Projekt> projektListeKopie = new ArrayList<Projekt>();
                ArrayList<Schueler> schuelerKopie = new ArrayList<Schueler>();
                for (Projekt p : projektListe) {
                    Projekt p2 = new Projekt(p.getId(), p.getminTeilnehmer(), p.getmaxTeilnehmer());
                    projektListeKopie.add(p2);
                }
                for (Schueler s : t.testSchueler) {
                    Projekt[] wunschKopie = new Projekt[s.getAnzahlGewaehlterProjekte()];
                    for (int m = 0; m < s.wuensche.length; m++) {
                        Projekt p = s.wuensche[m];
                        for (Projekt p2 : projektListeKopie) {
                            if (p2.getId() == p.getId()) {
                                wunschKopie[m] = p2;
                                break;
                            }
                        }
                    }
                    Schueler s2 = new Schueler(s.getId(), s.getName(), s.getVorname(), s.getKlasse(), wunschKopie, s.getIntKlasse());
                    schuelerKopie.add(s2);
                }
                //Verteilt die Schueler auf Projekte.
                Verteilung verteilung2 = new Verteilung(schuelerKopie, projektListeKopie);
                verteilung2.mischeSchueler();
                verteilung2.verteile4(true);
                //Speichert nur beste Verteilung ab
                double v_score = verteilung2.getScore();
                if (v_score < score) {
                    score = v_score;
                    bestVerteilung = verteilung2;
                }
                //verteilung2.macheAusgabe(false);
            }
            //Gibt beste Verteilung ab
            //Beste Verteilung
            //bestVerteilung.macheAusgabe(false);
            sum += bestVerteilung.anzahlAnWahlen();

        }
        //Gibt den Durschnitt der sinnvollen Projektwuensche aus.
        System.out.println("Durschnitt: " + sum / durchlaeufe);
    }

}

class Verteilungsthread implements Runnable {
    /**
     * Interner Thread
     */
    private Thread t;
    /**
     * Name des Threads
     */
    private String threadName;
    /**
     * Liste der Schueler, die verteilt werden sollen
     */
    private ArrayList<Schueler> schueler;
    /**
     * Liste der Projekte, auf die die Schueler verteilt werden sollen
     */
    private ArrayList<Projekt> projekte;
    /**
     * Anzahl der Verteilungen, die dieser Thread ausfuehren soll
     */
    private int durchgaenge;
    /**
     * Intern gespeicherte beste Verteilung
     */
    Verteilung bestVerteilung;

    /**
     * Diese Klasse wird zum Multithreaden benutzt, sodass alle Kerne der CPU benutzt werden koennen
     *
     * @param name              Name des Threads
     * @param schueler          Liste der Schueler, die verteilt werden sollen
     * @param projekte          Liste der Projekte
     * @param anzahlDurchgaenge Anzahl der Verteilungen, die dieser Thread ausfuehren soll
     */
    Verteilungsthread(String name, ArrayList<Schueler> schueler, ArrayList<Projekt> projekte, int anzahlDurchgaenge) {
        this.threadName = name;
        this.schueler = schueler;
        this.projekte = projekte;
        this.durchgaenge = anzahlDurchgaenge;
    }

    /**
     * Berechnet die gegebene Anzahl der Verteilungen
     */
    public void run() {
        System.out.println("Running " + threadName);
        double minScore = 10000;
        for (int i = 0; i < this.durchgaenge; i++) {
            if (i % 10000 == 0) {
                if (this.threadName.equals("Thread 0")) {
                    System.out.println((100 * (i + 0.0)) / this.durchgaenge + " % done!");
                    try {
                        FileWriter fw = new FileWriter(new File("prozentzahl"));
                        fw.write("" + ((i + 0.0) / this.durchgaenge));
                        fw.close();
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            }
            //Beide Listen kopieren und Objekte kopieren, damit Objekte bei der Verteilung nicht endgueltig zerstoert werden
            ArrayList<Projekt> projektListeKopie = new ArrayList<Projekt>();
            ArrayList<Schueler> schuelerKopie = new ArrayList<Schueler>();
            for (Projekt p : this.projekte) {
                Projekt p2 = new Projekt(p.getId(), p.getminTeilnehmer(), p.getmaxTeilnehmer());
                projektListeKopie.add(p2);
            }
            for (Schueler s : this.schueler) {
                Projekt[] wunschKopie = new Projekt[s.getAnzahlGewaehlterProjekte()];
                for (int m = 0; m < s.wuensche.length; m++) {
                    Projekt p = s.wuensche[m];
                    for (Projekt p2 : projektListeKopie) {
                        if (p2.getId() == p.getId()) {
                            wunschKopie[m] = p2;
                            break;
                        }
                    }
                }
                Schueler s2 = new Schueler(s.getId(), s.getName(), s.getVorname(), s.getKlasse(), wunschKopie, s.getIntKlasse());
                schuelerKopie.add(s2);
            }

            //Neue Verteilung anlegen, daten mischen und Algorithmus auswählen
            Verteilung verteilung = new Verteilung(schuelerKopie, projektListeKopie);
            verteilung.mischeSchueler();
            verteilung.verteile4(true);
            //verteilung.macheAusgabe();

            //Nur beste Verteilung wird gespeichert
            double score = verteilung.getScore();
            if (score < minScore) {
                bestVerteilung = verteilung;
                minScore = score;
            }
        }
        System.out.println("Thread " + threadName + " exiting.");
        Main.incrementCoresFinished();
    }

    /**
     * Startet den Thread
     */
    public void start() {
        System.out.println("Starting " + threadName);
        if (t == null) {
            t = new Thread(this, threadName);
            t.start();
        }
    }
}
