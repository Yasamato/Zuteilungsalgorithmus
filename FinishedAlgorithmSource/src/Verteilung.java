import javax.sound.midi.Soundbank;
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
                            Schueler s2 = p.getSchuelerDessenNteWahlNochFreiIst(j, this.projektListe);
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
            int countPlaetze = 0;
            ArrayList<Projekt> zuloeschen = new ArrayList<Projekt>();
            for (Projekt p : this.projektListe) {
                if (p.getTeilnehmer().size() < p.getminTeilnehmer()) {
                    zuloeschen.add(p);
                    countPlaetze += p.getmaxTeilnehmer();
                    for (int i = 0; i < p.getTeilnehmer().size(); ) {
                        Schueler s = p.getTeilnehmer().get(i);
                        s.schreibeAusProjektAus();
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
     * Verteilt Schueler auf Projekte
     * 1. Schritt: Jeder Schueler bekommt seinen Erstwunsch zugeteilt
     * 2. Schritt: Solange es noch Projekte gibt, die ueberfuellt sind:
     * Sei Projekt p ein solches Projekt:
     * Aus p werden nun wiederholt Schueler entzogen, bis p nicht mehr ueberfuellt ist.
     * Die entzogenen Schueler bekommen ihr als naechstes gewuenschtes Projekt zugeteilt.
     * Gibt es kein naechstes gewuenschtes Projekt, bleiben die Schueler zunaechst unverteilt(dead)
     * 3. Schritt: Solange es noch Projekte gibt, die nicht stattfinden:
     * Sei Projekt p ein solches Projekt:
     * Ist die Anzahl der verfuegbaren Projektplaetze ohne p kleiner als die Anzahl der Schueler, so muss p stattfinden.
     * Daher wird die benoetigte Anzahl an Schuelern in p hineingetauscht
     * Ansonsten wird geschaut, ob es sich mehr lohnt, Leute in p reinzutauschen oder alle Schueler aus p rauszutauschen. Dies wird dann gemacht
     * 4. Schritt: Fuer jeden Schueler wird noch einmal geschaut, ob eine hoehere Wahl noch ein besseres Ergebnis liefert
     * 5. Schritt: Fuer jeden Schueler wird fuer jedes Projekt, dass er lieber mag, noch fuer jeden dort eingeteilten Schueler geschaut, ob sich ein Tausch lohnt
     */
    public void verteile5() {
        int projektPlaetze = getAnzahlProjektplaetze();
        int anzahlWahlen = this.schuelerListe.get(0).wuensche.length;
        if (projektPlaetze < this.schuelerListe.size()) {
            System.exit(-1);
        }

        for (Schueler s : this.schuelerListe) {
            s.teileProjektZu(s.getWahl(1));
        }
        ArrayList<Projekt> sortierteProjekte = beliebtheitsListe(this.schuelerListe);
        ArrayList<Schueler> deadSchueler = new ArrayList<>();
        ArrayList<Schueler> collectedSchueler = new ArrayList<>();
        do {
            collectedSchueler.clear();
            for (Projekt p : this.projektListe) {
                if (p.getTeilnehmer().size() > p.getmaxTeilnehmer()) {
                    int ueberschuessige = p.getTeilnehmer().size() - p.getmaxTeilnehmer();
                    for (int i = 0; i < ueberschuessige; i++) {
                        Schueler removed = drawSchlechtesten(p, sortierteProjekte);
                        removed.schreibeAusProjektAus();
                        collectedSchueler.add(removed);
                        int pos = removed.getWahlPosition(p);
                        assert (pos != -1);
                        if (pos == removed.wuensche.length) {
                            deadSchueler.add(removed);
                        } else {
                            removed.teileProjektZu(removed.getWahl(pos + 1));
                        }
                    }
                }
            }
        } while (collectedSchueler.size() != 0);
        ArrayList<Projekt> beliebteste = beliebtheitsListe(deadSchueler);
        Iterator<Projekt> projektIterator = this.projektListe.iterator();
        Projekt p;
        while (projektIterator.hasNext()) {
            p = projektIterator.next();
            if (p.getTeilnehmer().size() < p.getminTeilnehmer()) {
                if (projektPlaetze - p.getmaxTeilnehmer() < this.schuelerListe.size()) {
                    //Müssen Schüler reingetauscht werden
                    ArrayList<Schueler> tauschKandidaten = getBesteTauschKandidaten(p, beliebteste, sortierteProjekte);
                    if (p.getTeilnehmer().size() + tauschKandidaten.size() < p.getminTeilnehmer()) {
                        System.exit(-1);
                    } else {
                        for (Schueler s : tauschKandidaten) {
                            s.schreibeAusProjektAus();
                            s.teileProjektZu(p);
                        }
                        assert (p.getTeilnehmer().size() >= p.getminTeilnehmer());
                        int vorher = deadSchueler.size();
                        platzFreiFuerSchueler(deadSchueler, anzahlWahlen);
                        int nachher = deadSchueler.size();
                        if (vorher != nachher) {
                            beliebteste = beliebtheitsListe(deadSchueler);
                        }
                    }
                } else {
                    //Was ist besser?
                    //1. Kandidaten in das Projekt reintraden
                    //2. Alle Projektteilnehmer raustraden
                    ArrayList<Schueler> reinTauschKandidaten = getBesteTauschKandidaten(p, beliebteste, sortierteProjekte);
                    double reinTauschScore = 100000000;
                    if (p.getTeilnehmer().size() + reinTauschKandidaten.size() >= p.getminTeilnehmer()) {
                        reinTauschScore = 0;
                        for (Schueler s : reinTauschKandidaten) {
                            assert (s.getWahlPosition(p) != -1);
                            reinTauschScore += Math.pow(s.getWahlPosition(p) - 1, 2);
                            //Minus Score davor
                            if (s.hatZugeteiltesProjekt()) {
                                reinTauschScore -= Math.pow(s.getWahlPosition(s.getZugeteiltesProjekt()) - 1, 2);
                            } else {
                                reinTauschScore -= anzahlWahlen * anzahlWahlen;
                            }
                        }
                    }
                    double rausTauschScore = 0;
                    HashMap<Projekt, Integer> imaginärGeaddeterTeilnehmer = new HashMap<>();
                    HashMap<Schueler, Projekt> imaginärZugeteilt = new HashMap<>();
                    for (int i = 0; i < anzahlWahlen; i++) {
                        for (Schueler s : p.getTeilnehmer()) {
                            if (imaginärZugeteilt.containsKey(s)) {
                                continue;
                            }
                            Projekt other = s.getWahl(i + 1);
                            if (other == p) {
                                continue;
                            }
                            int toAdd = imaginärGeaddeterTeilnehmer.getOrDefault(other, 0);
                            if (other.getTeilnehmer().size() + toAdd < other.getmaxTeilnehmer()) {
                                imaginärZugeteilt.put(s, other);
                                rausTauschScore += Math.pow(i, 2);
                                imaginärGeaddeterTeilnehmer.put(other, toAdd + 1);
                            }
                        }
                    }
                    for (Schueler s : p.getTeilnehmer()) {
                        rausTauschScore -= Math.pow(s.getWahlPosition(s.getZugeteiltesProjekt()) - 1, 2);
                        if (!imaginärZugeteilt.containsKey(s)) {
                            rausTauschScore += Math.pow(anzahlWahlen, 2);
                        }
                    }
                    if (reinTauschScore <= rausTauschScore) {
                        //Rein tauschen
                        for (Schueler s : reinTauschKandidaten) {
                            s.schreibeAusProjektAus();
                            s.teileProjektZu(p);
                        }
                        assert (p.getTeilnehmer().size() >= p.getminTeilnehmer());
                        int vorher = deadSchueler.size();
                        platzFreiFuerSchueler(deadSchueler, anzahlWahlen);
                        int nachher = deadSchueler.size();
                        if (vorher != nachher) {
                            beliebteste = beliebtheitsListe(deadSchueler);
                        }
                    } else {
                        //Raus tauschen
                        projektIterator.remove();
                        ArrayList<Schueler> deleteLater = new ArrayList<>();
                        for (Schueler s : p.getTeilnehmer()) {
                            Projekt tausch = imaginärZugeteilt.getOrDefault(s, null);
                            if (tausch != null) {
                                deleteLater.add(s);
                            } else {
                                deadSchueler.add(s);
                            }
                        }
                        for (Schueler s : deleteLater) {
                            s.schreibeAusProjektAus();
                            Projekt tausch = imaginärZugeteilt.getOrDefault(s, null);
                            s.teileProjektZu(tausch);
                        }
                        int vorher = deadSchueler.size();
                        platzFreiFuerSchueler(deadSchueler, anzahlWahlen);
                        int nachher = deadSchueler.size();
                        if (vorher != nachher) {
                            beliebteste = beliebtheitsListe(deadSchueler);
                        }
                    }
                }
            }
        }
        for (int i = 1; i <= anzahlWahlen; i++) {
            for (Schueler s : this.schuelerListe) {
                if (s.hatZugeteiltesProjekt() && s.getWahlPosition(s.getZugeteiltesProjekt()) <= i) {
                    continue;
                }
                Projekt z = s.getZugeteiltesProjekt();
                if (z == null || z.getTeilnehmer().size() > z.getminTeilnehmer()) {
                    Projekt projekt = s.getWahl(i);
                    if (projekt.getTeilnehmer().size() < projekt.getmaxTeilnehmer()) {
                        if (s.hatZugeteiltesProjekt()) {
                            s.schreibeAusProjektAus();
                        }
                        s.teileProjektZu(projekt);
                        continue;
                    }
                }
            }
        }

        boolean tausch = true;
        A:
        while (tausch) {
            tausch = false;
            for (Schueler s : this.schuelerListe) {
                if (s.hatZugeteiltesProjekt()) {
                    Projekt projekt = s.getZugeteiltesProjekt();
                    int wahl = s.getWahlPosition(projekt);
                    for (int i = 1; i < wahl; i++) {
                        Projekt iteWahl = s.getWahl(i);
                        //Positivauswirkung auf Score
                        int plusScore = (wahl - 1) * (wahl - 1) - (i - 1) * (i - 1);
                        double bestTausch = -1000000000;
                        Schueler bestTauscher = null;
                        for (Schueler s2 : iteWahl.getTeilnehmer()) {
                            int wahls2 = s2.getWahlPosition(iteWahl);
                            int welcheWahlIstProjekt = s2.getWahlPosition(projekt);
                            if (welcheWahlIstProjekt == -1) {
                                continue;
                            }
                            double minusScore = (wahls2 - 1) * (wahls2 - 1) - (welcheWahlIstProjekt - 1) * (welcheWahlIstProjekt - 1);
                            if (plusScore + minusScore > bestTausch) {
                                bestTausch = plusScore + minusScore;
                                bestTauscher = s2;
                            }
                        }
                        if (bestTauscher != null && bestTausch > 0) {
                            Projekt s2P = bestTauscher.getZugeteiltesProjekt();
                            bestTauscher.schreibeAusProjektAus();
                            bestTauscher.teileProjektZu(projekt);
                            s.schreibeAusProjektAus();
                            s.teileProjektZu(s2P);
                            tausch = true;
                            continue A;
                        }
                    }
                }
            }
        }
    }

    /**
     * Schaut, ob ein gewaehltes Projekt eines noch nicht zugeteilten Schuelers noch freie Plaetze hat. Ist dies der Fall,
     * so wird der Schueler dort zugeteilt.
     *
     * @param deadSchueler Noch nicht zugeteilte Schueler
     * @param anzahlWahlen Die Anzahl der abgegebenen Wuensche
     */
    public void platzFreiFuerSchueler(ArrayList<Schueler> deadSchueler, int anzahlWahlen) {
        for (int i = 0; i < anzahlWahlen; i++) {
            Iterator<Schueler> dSI = deadSchueler.iterator();
            Schueler s;
            while (dSI.hasNext()) {
                s = dSI.next();
                Projekt p = s.getWahl(i + 1);
                if (p.getTeilnehmer().size() < p.getmaxTeilnehmer()) {
                    s.teileProjektZu(p);
                    dSI.remove();
                }
            }
        }
    }

    /**
     * Berechnet die Beliebtheit der Projekte und gibt eine nach Beliebtheit aufsteigend sortierte Liste der Projekte zurueck.
     *
     * @param schueler Die Schueler, die untersucht werden sollen
     * @return Nach Beliebtheit sortierte Liste
     */
    public ArrayList<Projekt> beliebtheitsListe(ArrayList<Schueler> schueler) {
        ArrayList<Projekt> res = (ArrayList<Projekt>) this.projektListe.clone();
        Collections.sort(res, new Comparator<Projekt>() {
            @Override
            public int compare(Projekt o1, Projekt o2) {
                double o1score = o1.getBeliebtheit(schueler);
                double o2score = o2.getBeliebtheit(schueler);
                if (o1score < o2score) {
                    return 1;
                } else if (o2score < o1score) {
                    return -1;
                }
                return 0;
            }
        });
        return res;
    }

    /**
     * Ermittelt die besten Tauschkandidaten fuer ein Projekt p, so dass dieses stattfinden kann.
     * Fehlen einem Projekt also noch 3 Teilnehmer, damit es mindestens stattfindet, werden hier 3 Tauschkandidaten gesucht.
     *
     * @param p                    Das betroffene Projekt
     * @param beliebtheitNachDead  Beliebtheitsliste nach nicht zugeteilten Schuelern
     * @param beliebtheitNachAllen Beliebtheitsliste nach allen Schuelern
     * @return Liste der Tauschkandidaten
     */
    public ArrayList<Schueler> getBesteTauschKandidaten(Projekt p, ArrayList<Projekt> beliebtheitNachDead, ArrayList<Projekt> beliebtheitNachAllen) {
        ArrayList<Schueler> tauschKandidaten = new ArrayList<>();
        int anzahl = p.getminTeilnehmer() - p.getTeilnehmer().size();
        for (int i = 0; i < anzahl; i++) {
            Schueler kandidat = this.getBestenTauschKandidaten(p, beliebtheitNachDead, beliebtheitNachAllen, tauschKandidaten);
            if (kandidat == null) {
                return tauschKandidaten;
            }
            tauschKandidaten.add(kandidat);
        }
        return tauschKandidaten;
    }

    /**
     * Es wird ein bereits zugeteilter Schueler gesucht, der noch am Glücklichsten damit waere, in Projekt p reingetauscht zu werden.
     * Es wird sichergestellt, dass dieser Schueler Projekt p gewaehlt hat. Gibt es keinen solcher Schueler, wird null returnt.
     * 1.Kriterium: Projekt p als hoechster Wunsch
     * 2.Kriterium: Das Projekt in dem sich der Schueler befindet, ist bei nicht zugeteilten Schuelern sehr beliebt
     * 3.Kriterium: Das Projekt in dem sich der Schueler befindet, ist bei allen Schuelern sehr beliebt
     * 4.Kriterium: Der Schueler passt gut zur Klassenstufe von p
     *
     * @param p                    Das Projekt, in welches getauscht werden soll
     * @param beliebtheitNachDead  Beliebtheitsliste nach nicht zugeteilten Schuelern
     * @param beliebtheitNachAllen Beliebtheitsliste nach allen Schuelern
     * @param bereitsGewaehlt      Schueler die bereits Tauschkandidaten sind
     * @return Bester Tauschkandidat, null wenn keiner vorhanden
     */
    public Schueler getBestenTauschKandidaten(Projekt p, ArrayList<Projekt> beliebtheitNachDead, ArrayList<Projekt> beliebtheitNachAllen, ArrayList<Schueler> bereitsGewaehlt) {
        Schueler bester = null;
        double erstRangigScore = 10000;
        double zweitRangigScore = 10000;
        double drittRangigScore = 10000;
        double viertRangigScore = 10000;
        for (Schueler s : this.schuelerListe) {
            if (s.hatZugeteiltesProjekt() && s.getZugeteiltesProjekt() == p || bereitsGewaehlt.contains(s)) {
                continue;
            }
            int wahl = s.getWahlPosition(p);
            if (wahl != -1) {
                if (wahl < erstRangigScore) {
                    erstRangigScore = wahl;
                    bester = s;
                } else if (wahl == erstRangigScore) {
                    int index = 100000000;
                    if (s.getZugeteiltesProjekt() != null) {
                        index = getIndexOf(beliebtheitNachDead, s.getZugeteiltesProjekt());
                    }
                    if (index < zweitRangigScore) {
                        zweitRangigScore = index;
                        bester = s;
                    } else if (index == zweitRangigScore) {
                        int index2 = 100000000;
                        if (s.getZugeteiltesProjekt() != null) {
                            index2 = getIndexOf(beliebtheitNachAllen, s.getZugeteiltesProjekt());
                        }
                        if (index2 < drittRangigScore) {
                            drittRangigScore = index;
                            bester = s;
                        } else if (index2 == drittRangigScore) {
                            double abweichung = Math.abs(s.getIntKlasse() - p.getDurchschnittKlasse());
                            if (abweichung < viertRangigScore) {
                                viertRangigScore = abweichung;
                                bester = s;
                            }
                        }
                    }
                }
            }


        }
        return bester;
    }

    /**
     * Ermittelt den Index eines Projekts in einer Projektliste
     *
     * @param liste Projektliste
     * @param p     Das zu suchende Projekt
     * @return Index, -1 falls nicht in Liste
     */
    public static int getIndexOf(ArrayList<Projekt> liste, Projekt p) {
        for (int i = 0; i < liste.size(); i++) {
            if (liste.get(i) == p) {
                return i;
            }
        }
        assert (false);
        return -1;
    }

    /**
     * Hier wird der Schueler von Projekt p gesucht, der P verlassen muss.
     * 1.Kriterium: P ist ein hoher(beliebter) Wunsch des Schuelers
     * 2.Kriterium: Das als naechsten Wunsch gewaehlte Projekt ist unbeliebt
     * 3.Kriterium: Der Schueler passt nicht gut zu P von seiner Klassenstufe
     *
     * @param p                 Das betroffene Projekt
     * @param beliebtheitsListe Beliebtheitsliste nach allen Schuelern
     * @return Schueler, der das Projekt verlaesst
     */
    public static Schueler drawSchlechtesten(Projekt p, ArrayList<Projekt> beliebtheitsListe) {
        Schueler schlechtester = null;
        double schlechtesterScore = 10000000;
        double schlechtesterScoreIndex = -1;
        for (Schueler s : p.getTeilnehmer()) {
            int currentWahl = s.getWahlPosition(p);
            if (currentWahl == -1) {
                currentWahl = s.wuensche.length + 1;
            }
            double score = currentWahl * currentWahl;
            double indexScore = -1;
            if (currentWahl != s.wuensche.length) {
                Projekt nextWahl = s.getWahl(currentWahl + 1);
                indexScore = getIndexOf(beliebtheitsListe, nextWahl);
            }
            if (score < schlechtesterScore) {
                schlechtesterScore = score;
                schlechtester = s;
                schlechtesterScoreIndex = indexScore;
            } else if (score == schlechtesterScore) {
                if (schlechtesterScoreIndex < indexScore) {
                    schlechtesterScore = score;
                    schlechtester = s;
                    schlechtesterScoreIndex = indexScore;
                } else if (schlechtesterScoreIndex == indexScore) {
                    double durchschnitt = p.getDurchschnittKlasse();
                    if (Math.abs(s.getIntKlasse() - durchschnitt) > Math.abs(schlechtester.getIntKlasse() - durchschnitt)) {
                        schlechtester = s;
                    }
                }
            }
        }
        assert (schlechtester != null);
        return schlechtester;
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
