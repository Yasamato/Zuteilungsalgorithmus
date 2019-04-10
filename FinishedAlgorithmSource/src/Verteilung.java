import java.util.*;


public class Verteilung {
    /**
     * Speichert die Liste aller Projekte
     */
    public ArrayList<Projekt> projektListe;
    /**
     * Speichert die Liste aller Schueler
     */
    public ArrayList<Schueler> schuelerListe;
    /**
     * Speichert die Anzahl der urspruenglichen Projekte, diese kann moeglicherweise kleiner werden, da unbeliebt Projekte moegl. nicht stattfinden.
     */
    private int anzUrspruenglicheP;

    /**
     * Klasse, die alle Methoden, die fuer die Verteilung an sich wichtig sind, zur Verfuegung stellt und den Algorithmus an sich bereit stellt.
     *
     * @param schuelerListe Liste aller Schueler, die verteilt werden sollen
     * @param projektListe  Liste der Projekte, auf welche die Schueler verteilt werden sollen
     */
    public Verteilung(ArrayList<Schueler> schuelerListe, ArrayList<Projekt> projektListe) {
        this.schuelerListe = schuelerListe;
        this.projektListe = projektListe;
        this.anzUrspruenglicheP = projektListe.size();
    }

    /**
     * Ermittelt alle Schueler, welche noch kein Projekt zugeteilt bekommen haben
     *
     * @return Liste der Schueler
     */
    public ArrayList<Schueler> getUnverteilteSchueler() {
        ArrayList<Schueler> unverteilt = new ArrayList<Schueler>();
        for (Schueler s : this.schuelerListe) {
            if (!s.hatZugeteiltesProjekt()) {
                unverteilt.add(s);
            }
        }
        return unverteilt;
    }

    /**
     * Ermittelt die Anzahl der Plaetze, die alle Projekte insgesamt maximal aufbringen koennen
     *
     * @return Anzahl der Plaetze
     */
    public int getAnzahlProjektplaetze() {
        int count = 0;
        for (Projekt p : this.projektListe) {
            count += p.getmaxTeilnehmer();
        }
        return count;
    }

    /**
     * Implementiert Algorithmus, der Algorithmus funktioniert folgendermassen:
     * Sei su die Menge aller unverteilen Schueler:
     * 1.Fuer jeden Schueler in SU:
     * Wenn der 1.Wunsch des Schuelers frei ist, teile ihn dort zu:
     * fuer n= 2, n kleiner gleich Anzahl der Wuensche des Schuelers, n+=1 pro Iteration:
     * Wenn der n.Wunsch des Schuelers frei ist, teile ihn dort zu, sonst pruefe fuer jedes seiner anderen  j kleiner n Wuensche ob im j.ten Wunschprojekt noch ein Schueler ist, dessen n.Wunsch noch frei ist. Wenn ja teile diesen Schueler seinem n.Wunsch zu, und teile dem urspruenglichem Schueler sein j.tes Projekt zu
     * Wenn 1.terminiert ist, ermittle alle Projekte, die nicht stattfinden koennen, da zu wenig Teilnehmer in dem Projekt sind.
     * Trage alle Teilnehmer aus solchen Projekten aus und loesche diese temporaer aus der allgemeinen Projektliste.
     * Ist nun die Anzahl der maximal moeglchen Plaetze aller Projekte kleiner als die Anzahl aller Schueler, fuege solange von den temporaer geloeschten Projekten das beliebtes Projekt zu der Projektliste hinzu, bis die Bedingung nicht mehr erfuellt ist.
     * Nun verteile die restlichen unverteilen Schuelern, wessen Projekte nun ausfallen noch einmal auf die uebrig gebliebenen Projekte zu.
     *
     * @param beachteProjekteDieNichtStattFinden Rekursionsanker, True wenn Projekte die nicht stattfinden geloescht werden sollen
     */
    public void verteile4(boolean beachteProjekteDieNichtStattFinden) {
        int projektplaetze = getAnzahlProjektplaetze();
        if (projektplaetze < this.schuelerListe.size()) {
            System.exit(-1);
        }
        ArrayList<Schueler> unverteilt = getUnverteilteSchueler();
        //System.out.println("Unverteilt: "+unverteilt.size());
        for (int i = 0; i < unverteilt.size(); i++) {
            Schueler s = unverteilt.get(i);
            A:
            for (int j = 1; j <= unverteilt.get(0).getAnzahlGewaehlterProjekte(); j++) {
                if (!projektListe.contains(s.getWahl(j))) continue;
                if (j == 1) {
                    if (s.wahlFrei(1)) {
                        s.teileProjektZu(s.getWahl(1));
                        unverteilt.remove(s);
                        i--;
                        break A;
                    }
                } else {
                    if (s.wahlFrei(j)) {
                        s.teileProjektZu(s.getWahl(j));
                        unverteilt.remove(s);
                        i--;
                        break A;
                    } else {

                        for (int m = j - 1; m >= 1; m--) {
                            Projekt p = s.getWahl(m);
                            Schueler s2 = p.getSchuelerDessenNteWahlNochFreiIst(j);
                            if (s2 != null) {
                                s2.schreibeAusProjektAus();
                                s2.teileProjektZu(s2.getWahl(j));
                                s.teileProjektZu(s.getWahl(m));
                                unverteilt.remove(s);
                                i--;
                                break A;
                            }
                        }
                    }
                }
            }

        }

        //Verschiebe Schueler die jetzt noch in einem Projekt sind, welches aufgrund der Mindestanzahl nicht stattfinden koennen.
        if (beachteProjekteDieNichtStattFinden) {
            ArrayList<Schueler> dieSchueler = new ArrayList<Schueler>();
            int countPlaetze = 0;
            ArrayList<Projekt> zuloeschen = new ArrayList<Projekt>();
            for (Projekt p : this.projektListe) {
                if (p.getTeilnehmer().size() < p.getminTeilnehmer()) {
                    zuloeschen.add(p);
                    countPlaetze += p.getmaxTeilnehmer();
                    for (int i = 0; i < p.getTeilnehmer().size(); ) {
                        Schueler s = p.getTeilnehmer().get(i);
                        s.schreibeAusProjektAus();
                        dieSchueler.add(s);
                    }
                }
            }
            for (Projekt p : zuloeschen) {
                this.projektListe.remove(p);
            }
            //System.out.println("Projektplaetze: "+projektplaetze);
            //System.out.println("Plaetze die ausfallen: "+countPlaetze);
            //System.out.println("Schueler: "+this.schuelerListe.size());
            if (projektplaetze - countPlaetze > this.schuelerListe.size()) {
                //System.out.println("hi");
                this.verteile4(false);
                //System.out.println("------------------------------------------------");
                //System.out.println("------------------------------------------------");
                //System.out.println("------------------------------------------------");
                /*for(Schueler s: dieSchueler){
                    System.out.println(s.getZugeteiltesProjekt());
				}*/
                //System.out.println("------------------------------------------------");
                //System.out.println("------------------------------------------------");
                //System.out.println("------------------------------------------------");
            } else {
                Verteilung v = this;
                Collections.sort(zuloeschen, new Comparator<Projekt>() {
                    @Override
                    public int compare(Projekt o1, Projekt o2) {
                        int o1B = o1.getBeliebtheit(v.schuelerListe);
                        int o2B = o2.getBeliebtheit(v.schuelerListe);
                        if (o1B < o2B) {
                            return -1;
                        } else if (o1B > o2B) {
                            return 1;
                        }
                        return 0;
                    }
                });
                while (projektplaetze - countPlaetze < this.schuelerListe.size()) {
                    Projekt p = zuloeschen.get(zuloeschen.size() - 1);
                    zuloeschen.remove(p);
                    this.projektListe.add(p);
                    countPlaetze -= p.getmaxTeilnehmer();
                }
                this.verteile4(false);
            }
        }
    }

    /**
     * Ermittelt den abstrakten Wert des Scores einer Verteilung. Ein Score einer Verteilung wird so berechnet:
     * score =  Durschnittliche Abweichung pro Schueler
     * mit Abweichung pro Schueler = Rang des Zugeteilten Projektes auf der Wunschliste des Schuelers-1 ins Quadrat
     * Somit ist die Abweichung 0, wenn der Schueler sein Erstwunsch bekommen hat, 1 wenn Zweitwunsch, 4 wenn Drittwunsch usw.
     *
     * @return Durschnittliche Abweichung pro Schueler
     */
    public double getScore() {
        double score = 0.0;
        for (Schueler s : this.schuelerListe) {
            if (s.getWahlPosition(s.getZugeteiltesProjekt()) == -1) {
                score += s.getAnzahlGewaehlterProjekte() * s.getAnzahlGewaehlterProjekte();
            } else {
                score += (s.getWahlPosition(s.getZugeteiltesProjekt()) - 1) * (s.getWahlPosition(s.getZugeteiltesProjekt()) - 1);
            }
        }
        return score;
    }

    /**
     * Ermittelt, wie viele Projektwuensche aus der Wunschliste jedes Schuelers maximal jemals beachtet wurden. Ein 8. Wunsch eines Schuelers kann beispielsweise niemals gebraucht werden.
     *
     * @return Anzahl der sinnvollen Projektwuensche
     */
    public int anzahlAnWahlen() {
        int highestPos = -1;
        int maxAnzahl = this.schuelerListe.get(0).getAnzahlGewaehlterProjekte() + 1;
        for (Schueler s : this.schuelerListe) {
            int wahl = s.getWahlPosition(s.getZugeteiltesProjekt());
            if (wahl == -1) {
                wahl = maxAnzahl;
            }
            if (wahl > highestPos) {
                highestPos = wahl;
            }
        }
        return highestPos;
    }

    /**
     * Macht eines Ausgabe zur Konsole, welche die Verteilung beschreibt
     *
     * @param printProjekte Wahrheitswert, ob jedes Projekt zusaetzlich noch beschrieben werden soll
     */
    public void macheAusgabe(boolean printProjekte) {
        ArrayList<Projekt> findetNichtStatt = new ArrayList<>();
        for (Projekt p : this.projektListe) {
            if (p.getTeilnehmer().size() < p.getminTeilnehmer()) {
                if (p.getTeilnehmer().size() != 0) {
                    System.out.println("Es gab noch Schüler die in ein nicht stattfindendes Projekt zugeteilt wurden.");
                    System.out.println("Trage aus...");
                    for (int i = 0; i < p.getTeilnehmer().size(); ) {
                        p.getTeilnehmer().get(i).schreibeAusProjektAus();
                    }
                }
                findetNichtStatt.add(p);
            }
        }
        for (Projekt p : findetNichtStatt) {
            this.projektListe.remove(p);
        }
        if (printProjekte) {
            for (Projekt p : this.projektListe) {
                System.out.println(p.toString());
            }
        }
        int count = 0;
        for (Schueler s : this.schuelerListe) {
            if (!s.hatZugeteiltesProjekt()) {
                count++;
            }
        }
        System.out.println("Anzahl Schueler ohne Projekt: " + count);
        int anzahlSchueler = schuelerListe.size();
        double[] prozente = new double[schuelerListe.get(0).getAnzahlGewaehlterProjekte() + 1];
        int[] counter = new int[schuelerListe.get(0).getAnzahlGewaehlterProjekte() + 1];
        for (Schueler s : this.schuelerListe) {
            int pos = s.getWahlPosition(s.getZugeteiltesProjekt());
            if (pos == -1) {
                counter[counter.length - 1]++;
            } else {
                counter[pos - 1]++;
            }
        }
        double score = 0.0;
        for (int i = 0; i + 1 < counter.length; i++) {
            prozente[i] = (counter[i] + 0.0) / anzahlSchueler * 100;
            score += i * i * counter[i];
            System.out.println("Schueler die die " + (i + 1) + " te Wahl bekommen haben: " + counter[i] + "/" + anzahlSchueler + "(" + (Math.round(prozente[i] * 100.0) / 100.0) + ")");
        }
        int i = counter.length - 1;
        prozente[i] = (counter[i] + 0.0) / anzahlSchueler * 100;
        score += i * i * counter[i];
        System.out.println("Schueler die keine ihrer Wahlen bekommen haben: " + counter[i] + "/" + anzahlSchueler + "(" + (Math.round(prozente[i] * 100.0) / 100.0) + ")");
        int zaehleSchuelerDieEinProjektHabenWelchesNichtStattFindet = 0;
        for (Projekt p : this.projektListe) {
            if (p.getTeilnehmer().size() < p.getminTeilnehmer()) {
                zaehleSchuelerDieEinProjektHabenWelchesNichtStattFindet += p.getTeilnehmer().size();
            }
        }
        System.out.println("Schueler die ein Projekt haben welches nicht stattfindet: " + zaehleSchuelerDieEinProjektHabenWelchesNichtStattFindet);
        System.out.println("Score: " + score + "(" + score / anzahlSchueler + ")");
        System.out.println("Anzahl der Projekte die stattfinden: " + this.projektListe.size() + " /" + anzUrspruenglicheP);
        double kummulierteStd = 0.0;
        for (Projekt p : this.projektListe) {
            kummulierteStd += p.getStd();
        }
        System.out.println("Std: " + kummulierteStd + " (" + kummulierteStd / this.projektListe.size() + ")");
    }

    /**
     * Mischt das Datenset der Schueler, damit bspw. Schueler, deren Nachname mit A anfagen keinen unfairen Vorteil bei der Zuteilung haben
     */
    public void mischeSchueler() {
        ArrayList<Schueler> gemischteSchuelerListe = new ArrayList<Schueler>();
        while (this.schuelerListe.size() > 0) {
            int rand = (int) (Math.random() * this.schuelerListe.size());
            Schueler s = this.schuelerListe.remove(rand);
            gemischteSchuelerListe.add(s);
        }
        this.schuelerListe = gemischteSchuelerListe;
    }
}
