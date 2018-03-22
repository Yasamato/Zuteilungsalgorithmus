import java.io.BufferedReader;
import java.io.FileReader;
import java.util.ArrayList;

public class SchuelerFileReader {
    /**
     * BufferedReader der Datei liest
     */
    private BufferedReader reader;
    /**
     * Liste aller Schueler in der Datei
     */
    private ArrayList<Schueler> schuelerList;
    /**
     * Liste aller Projekte, die die Schueler potentiell als Wunsch angeben koennen
     */
    private ArrayList<Projekt> projektListe;
    /**
     * String zur Festlegung des Aufbaus der CSV-Datei.
     */
    private String regex;

    /**
     * Klasse zum auslesen einer CSV-Datei nach Schuelern.
     * Initialisiert alle Attribute
     *
     * @param source       Pfad zur Datei
     * @param regex        Beschreibung des Aufbaus der CSV-Datei
     * @param projektListe Liste aller Projekte, die die Schueler potentiell als Wunsch angeben koennen
     */
    public SchuelerFileReader(String source, String regex, ArrayList<Projekt> projektListe) {
        this.projektListe = projektListe;
        this.regex = regex;
        try {
            reader = new BufferedReader(new FileReader(source));
            String line = null;

        } catch (Exception e) {
            reader = null;
            e.printStackTrace();
            System.exit(0);
        }
        schuelerList = null;
    }

    /**
     * Liest alle Schueler
     *
     * @return Alle Schueler, die in der CSV-Datei sind
     */
    public ArrayList<Schueler> getSchueler() {
        if (schuelerList != null) {
            return schuelerList;
        } else {
            System.out.println("Lade schueler");
            loadSchueler();
            return schuelerList;
        }

    }

    /**
     * Sucht ein Projekt einer bestimmten ID in der Projektliste
     *
     * @param id ID des Projektes
     * @return das Projekt mit der dazugehoerigen ID, null wenn kein Projekt die angegebene ID hat
     */
    private Projekt getProjekt(int id) {
        for (Projekt p : this.projektListe) {
            if (p.getId() == id) {
                return p;
            }
        }
        // System.out.println(id);
        // assert(false);
        return null;
    }

    /**
     * Liest alle Schueler ein
     */
    private void loadSchueler() {
        schuelerList = new ArrayList<Schueler>();
        String line;
        Schueler aktSchueler;
        // int i = 0;
        int count = 0;
        try {
            //Indizes
            int klasse_i = this.regex.indexOf("K");
            int name_i = this.regex.indexOf("N");
            int vorname_i = this.regex.indexOf("V");
            ArrayList<Integer> wunsch_i = new ArrayList<Integer>();
            int num = 1;
            if(klasse_i ==-1 || name_i ==-1 || vorname_i==-1){
            	System.out.println("Falsche Eingabe");
            	System.exit(0);
            }
            while (this.regex.contains("" + num)) {
                wunsch_i.add(this.regex.indexOf("" + num));
                num++;
            }
            A:
            while ((line = reader.readLine()) != null) {
                count++;
                String[] elemente = line.split(",");
                String klasse = elemente[klasse_i];
                String name = elemente[name_i];
                String vorname = elemente[vorname_i];
                String[] strWuensche = new String[wunsch_i.size()];
                //System.out.println("Klasse: "+klasse);
                //System.out.println("Name: "+name);
                for (int i = 0; i < strWuensche.length; i++) {
                    strWuensche[i] = elemente[wunsch_i.get(i)];
                    //System.out.println("Wunsch "+strWuensche[i]);
                }
                int intKlasse = -1;
                // System.out.println("Klasse: "+klasse+ " Name: "+name+
                // " Vorname: "+vorname);
                Projekt[] wuensche = new Projekt[strWuensche.length];
                try {
                    for (int i = 0; i < wuensche.length; i++) {
                        wuensche[i] = getProjekt(Integer.parseInt(strWuensche[i]));
                    }
                    for (Projekt p : wuensche) {
                        if (p == null) {
                            continue A;
                        }
                    }
                    if (klasse == "Austausch") {
                        intKlasse = 10;
                    } else {
                        intKlasse = Integer.parseInt(klasse.toLowerCase()
                                .replaceAll("[a-z]*", ""));
                    }
                    aktSchueler = new Schueler(count, name, vorname, klasse,
                            wuensche, intKlasse);
                    schuelerList.add(aktSchueler);

                } catch (Exception e) {
                    // System.out.println("Fehler bei int-cast in Zeile " +
                    // count);
                    // e.printStackTrace();
                }

            }
            System.out.println("Anzahl der Schueler mit korrekter Abgabe, und ohne Sonderprojekt: " + schuelerList.size());
        } catch (Exception e) {
            e.printStackTrace();
        }

    }

}
