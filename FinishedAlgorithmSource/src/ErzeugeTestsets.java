import java.util.ArrayList;
import java.util.HashMap;
import java.util.SortedSet;
import java.util.TreeSet;


public class ErzeugeTestsets {
    /**
     * Speichert erzeugte Schueler
     */
    ArrayList<Schueler> testSchueler;
    /**
     * Speichert die originalen Schueler
     */
    ArrayList<Schueler> unerweitertesTestSetSchueler;
    /**
     * Speichert die originalen Projekte
     */
    ArrayList<Projekt> unerweiteretesTestSetProjekt;
    /**
     * Speichert die Beliebtheit eines Projektes
     * Die Beliebtheit eines Projektes: B = m1* 4 + m2*3 + m3*2 + m4
     * Mit m1 = Anzahl der Menschen die dieses Projekt als 1. Wunsch haben usw.
     */
    HashMap<Projekt, Integer> beliebtheit = new HashMap<Projekt, Integer>();
    /**
     * Kummulierte Beliebtheit der Values aus der HashMap beliebtheit
     */
    int kummulierteBeliebtheit;

    /**
     * Klasse zum zufaelligen erzeugen Testsets bestimmter Groesse
     *
     * @param anzahlSchueler Die Anzahl der Schueler, die das neue Testset haben soll
     * @param schuelerWahlen Die ArrayList der Schueler, aus welcher die Wuensche der Schueler gelesen werden, woraus das Testset so erzeugt wird, dass die Beliebtheit jedes Projektes dennoch ungefaehr gleich bleibt
     * @param projekte       Die ArrayList der verfuegbaren Projekte
     */
    public ErzeugeTestsets(int anzahlSchueler, ArrayList<Schueler> schuelerWahlen, ArrayList<Projekt> projekte) {
        this.unerweitertesTestSetSchueler = schuelerWahlen;
        this.unerweiteretesTestSetProjekt = projekte;
        this.initBeliebtheit();
        this.testSchueler = new ArrayList<Schueler>();
        for (int i = 0; i < anzahlSchueler; i++) {
            testSchueler.add(getRandomSchueler(i));
        }
    }

    /**
     * Generiert einen zufaelligen Schueler.
     * Dabei haengen die zufaellig genierten Wuensche von der Beliebtheit der Projekte aus dem urspruenglichen Set ab.
     * Genauer hat jedes Projekt p der Beliebtheit b_p die Wahrscheinlichkeit P(b_p) = b_p / kummulierteBeliebtheit, als erst Wunsch aufzutreten.
     * Sei b_p1 die Beliebtheit des Projektes p1, welches nun als ErstWunsch ausgewaehlt wurde.
     * Dann hat jedes Projekt p, ausser p1, der Beliebtheit b_p die Wahrscheinlichkeit P(b_p) = b_p / (kummulierteBeliebtheit-b_p1)
     * Diese Reihe sei so weiter zu fuehren.
     *
     * @param id Id des neuen Schuelern;  wird einfach hochgezaehlt
     * @return Returnt einen zufaellig genierten Schueler mit zufaelligen Wuenschen, wobei die Wuensche von der Beliebtheit des urspruenglichen Testsets abhaengen.
     */
    public Schueler getRandomSchueler(int id) {
        int _id = id;
        String _vorname = id + "";
        String _nachname = id + "";
        String _klasse = id + "";
        //Ein zufaellig genierter Schueler hat automatisch normalerweise 8 Wuensche.
        Projekt[] wuensche = new Projekt[8];
        int minusBeliebtheit = 0;
        for (int i = 0; i < wuensche.length; i++) {
            //System.out.println("I: "+i);

            HashMap<Double, Projekt> summierteWahrscheinlichkeiten = new HashMap<Double, Projekt>();
            double latest = 0.0;
            //Zieht zufaelliges Projekt
            for (Projekt p : this.unerweiteretesTestSetProjekt) {
                if (!contains(wuensche, p)) {
                    double relativeB = (beliebtheit.get(p) + 0.0) / (kummulierteBeliebtheit - minusBeliebtheit);
                    if (relativeB == 0.0) {
                        relativeB = 0.00000000000001;
                    }
                    summierteWahrscheinlichkeiten.put(latest + relativeB, p);
                    latest += relativeB;
                }
            }
            double rand = Math.random() * 0.999999999999999;
            double latestD = 0.0;
            SortedSet<Double> keys = new TreeSet<Double>(summierteWahrscheinlichkeiten.keySet());
            boolean found = false;
            for (Double d : keys) {
                //System.out.println(d);
                if (rand >= latestD && rand <= d) {
                    wuensche[i] = summierteWahrscheinlichkeiten.get(d);
                    minusBeliebtheit += beliebtheit.get(wuensche[i]);
                    found = true;
                    break;
                }
                latestD = d;
            }
            //Sollte nicht vorkommen
            if (!found) {
                assert (false);
                //System.out.println("Rand: "+rand);
                //System.out.println("hi");
            }
        }
        return new Schueler(_id, _nachname, _vorname, _klasse, wuensche, -1);
    }

    /**
     * Gibt zurueck, ob ein bestimmtes Projekt in einer Wunschliste ist, oder nicht
     *
     * @param wunsch Wunschliste
     * @param p2     Projekt, welches in der Wunschliste sein sollte
     * @return Wahrheitswert ob Projekt in der Wunschliste ist
     */
    public boolean contains(Projekt[] wunsch, Projekt p2) {
        for (Projekt p : wunsch) {
            if (p2.equals(p)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Initialisiert die Beliebtheit-HashMap mit Beliebtheit b_p eines Projekts p=
     * b_p = 4*m1+3*m2 + 2*m3+m4 mit m1 = Anzahl der Schueler, die dieses Projekt als Erstwahl haben usw.
     * Initialisiert ausserdem das Attribut kummuliterteBeliebtheit.
     */
    public void initBeliebtheit() {
        for (Projekt p : this.unerweiteretesTestSetProjekt) {
            int beliebt = p.getBeliebtheit(this.unerweitertesTestSetSchueler);
            kummulierteBeliebtheit += beliebt;
            beliebtheit.put(p, beliebt);
        }
    }


}