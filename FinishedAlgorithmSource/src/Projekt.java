import java.util.ArrayList;


public class Projekt {
    /**
     * Speichert ID des Projektes
     */
    private String id;
    /**
     * Speichert minimale Anzahl der Teilnehmer, ohne welche das Projekt nicht stattfinden kann
     */
    private int minTeilnehmer;
    /**
     * Speichert die maximale Anzahl der Teilnehmer, welche in das Projekt passen
     */
    private int maxTeilnehmer;
    /**
     * Speichert die aktuelle zugeteilten Teilnehmer(Schueler)
     */
    private ArrayList<Schueler> teilnehmer;

    /**
     * Initialisiert Attribute
     *
     * @param id            ID des Projektes
     * @param minTeilnehmer minimale Anzahl der Teilnehmer
     * @param maxTeilnehmer maximale Anzahl der Teilnehmer
     */
    public Projekt(String id, int minTeilnehmer, int maxTeilnehmer) {
        this.id = id;
        this.minTeilnehmer = minTeilnehmer;
        this.maxTeilnehmer = maxTeilnehmer;
        this.teilnehmer = new ArrayList<Schueler>();
    }

    /**
     * Ermittelt Mittelwert der Klassenstufe aller Teilnehmer eines Projektes
     *
     * @return Mittelwert der Klassenstufe aller Teilnehmer eines Projektes
     */
    public double getDurchschnittKlasse() {
        double durschnitt = 0.0;
        for (Schueler s : this.teilnehmer) {
            durschnitt += s.getIntKlasse();

        }
        durschnitt /= this.teilnehmer.size();
        return durschnitt;
    }

    /**
     * Ermittelt Standardabweichung zu dem mittleren Wert der Klassenstufe eines Projektes
     *
     * @return Wert der Standardabweichung
     */
    public double getStd() {
        double res = 0.0;
        //Durschnittliche Klasse
        double durschnitt = getDurchschnittKlasse();
        for (Schueler s : this.teilnehmer) {
            res += Math.pow(s.getIntKlasse() - durschnitt, 2);
        }
        return res / this.teilnehmer.size();
    }

    /**
     * Loescht einen Teilnehmer/Schueler aus dem Projekt bzw. aus der Teilnehmerliste
     *
     * @param s Zu loeschender Schueler
     */
    public void removeTeilnehmer(Schueler s) {
        assert (this.teilnehmer.contains(s));
        this.teilnehmer.remove(s);
    }

    /**
     * @return Attribut minTeilnehmer
     */
    public int getminTeilnehmer() {
        return this.minTeilnehmer;
    }

    /**
     * @return Attribut maxTeilnehmer
     */
    public int getmaxTeilnehmer() {
        return this.maxTeilnehmer;
    }

    /**
     * @return Attribut teilnehmer
     */
    public ArrayList<Schueler> getTeilnehmer() {
        return this.teilnehmer;
    }

    /**
     * Ermittelt einen Schueler, der in diesem Projekt ist und dessen n-tes Wunschprojekt noch nicht voll ist. Dabei wird nicht der erstbeste Schueler ermittelt, sondern der Schueler, der am meisten vom durschnittlichen Mittelwert der Klassenstufe des Projektes abweicht.
     * So wird die Standardabweichung der Klassenstufen eines Projektes moeglichst niedrig gehalten.
     *
     * @param n die n-te Wahl, die bei den Schuelern noch frei.
     * @return Schueler, der aus diesem Projekt rausgetauscht werden kann
     */
    public Schueler getSchuelerDessenNteWahlNochFreiIst(int n, ArrayList<Projekt> projekte) {
        ArrayList<Schueler> alleWoNochFrei = new ArrayList<Schueler>();
        for (Schueler s : this.teilnehmer) {
            if (s.wahlFrei(n)&&projekte.contains(s.getWahl(n))) {
                alleWoNochFrei.add(s);
            }
        }
        if (alleWoNochFrei.size() == 0) {
            return null;
        } else {
            double durschnitt = getDurchschnittKlasse();
            double maxAb = -1;
            Schueler maxSchueler = null;
            for (Schueler s : alleWoNochFrei) {
                double abweichung = Math.pow((s.getIntKlasse() - durschnitt), 2);
                if (abweichung > maxAb) {
                    maxAb = abweichung;
                    maxSchueler = s;
                }
            }
            assert (maxSchueler != null);
            return maxSchueler;
        }
    }

    /**
     * @return Attribut ID
     */
    public String getId() {
        return id;
    }

    /**
     * Ermittelt, ob die Aktuelle Teilnehmerzahl gleich der maximalen Teilnehmerzahl entspricht.
     *
     * @return Boolean, ob das Projekt voll ist
     */
    public boolean istVoll() {
        return this.teilnehmer.size() >= maxTeilnehmer;
    }

    /**
     * Fuegt einen Schueler zur Teilnehmerliste hinzu, solange das Projekt noch nicht voll ist.
     *
     * @param s Schueler, der hinzugefuegt werden soll
     */
    public void addTeilnehmer(Schueler s) {
        assert (!this.istVoll());
        this.teilnehmer.add(s);
    }

    /**
     * Ermittelt, ob zwei Projekte die gleiche ID haben, wenn ja sind sie die selben Objekte, nach unsere Definition
     *
     * @param o Anderes Projekt-Objekt
     * @return Boolean, ob anderes Objekt gleich diesem ist
     */
    @Override
    public boolean equals(Object o) {
        if (o instanceof Projekt) {
            Projekt other = (Projekt) o;
            return other.id == this.id;
        }
        return false;
    }

    /**
     * Ermittelt Beliebtheit des Projektes fuer bestimmte Schueler, wobei die Beliebtheit: b= 4*m1+3*m2+2*m3+m4, wobei m1 = Anzahl der Schueler aus dem Schuelerset, die dieses Projekt als Erstwahl haben
     *
     * @param schuelerSet Liste der Schueler, fuer welche das Projekt eine bestimmte Beliebtheit erhalten soll
     * @return Beliebtheitswert
     */
    public int getBeliebtheit(ArrayList<Schueler> schuelerSet) {
        int beliebtheit = 0;
        for (Schueler s : schuelerSet) {
            if (s.getWahlPosition(this) != -1) {
                int position = s.getWahlPosition(this);
                if (position == 1) {
                    beliebtheit += 4;
                } else if (position == 2) {
                    beliebtheit += 3;
                } else if (position == 3) {
                    beliebtheit += 2;
                } else {
                    beliebtheit++;
                }
            }
        }
        return beliebtheit;
    }

    /**
     * Bildet einen String, der dieses Projekt beschreibt
     *
     * @return Beschreibungsstring
     */
    @Override
    public String toString() {
        String s = "-------------------------------------------------------------\n";
        s += "Projekt Nr. " + id + " Min. Teilnehmer: " + minTeilnehmer + " Max.Teilnehmer: " + maxTeilnehmer + " Anzahl Teilnehmer: " + this.teilnehmer.size() + ",Mittlere Klassenstufe: " + getDurchschnittKlasse() + " Std: " + this.getStd() + "\n";
        for (Schueler sch : this.teilnehmer) {
            s += "Schueler " + sch.getName() + "(" + sch.getWahlPosition(this) + ") Wahl" + " , Klasse:  " + sch.getIntKlasse() + "\n";
        }
        return s;
    }
}
