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
     * Trennzeichen der CSV-Datei
     */
    private String delimiter;

    /**
     * Spaltenindex in der CSV-Datei, der angibt, in welcher Klasse ein Schüler ist
     */
    private int klassenIndex;

    /**
     * Spaltenindex in der CSV-Datei, der den Nachnamen eines Schülers angibt
     */
    private int namenIndex;

    /**
     * Spaltenindex in der CSV-Datei, der den Vornamen eines Schülers angibt
     */
    private int vornamenIndex;

    /**
     * Spalteindizes in der CSV-Datei, die die Wünsche eines Schülers angeben
     */
    private ArrayList<Integer> wunschIndizes;

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
        this.validateRegex();
        try {
            reader = new BufferedReader(new FileReader(source));
        } catch (Exception e) {
            reader = null;
            e.printStackTrace();
            System.exit(0);
        }
    }

    /**
     * Validiert die Beschreibung der CSV-Datei
     */
    public void validateRegex() {
        if (this.regex.length() == 0) {
            System.out.println("Ungültige Beschreibung der CSV-Datei, zu wenig Zeichen");
            System.exit(0);
        }
        this.delimiter = this.regex.charAt(0) + "";
        this.regex = this.regex.substring(1, this.regex.length());
        this.klassenIndex = this.regex.indexOf("K");
        if (this.klassenIndex < 0) {
            System.out.println("Falsche oder fehlende Eingabe für die Klassenspalte");
            System.exit(0);
        }
        this.namenIndex = this.regex.indexOf("N");
        if (this.namenIndex < 0) {
            System.out.println("Falsche oder fehlende Eingabe für die Namenspalte");
            System.exit(0);
        }
        this.vornamenIndex = this.regex.indexOf("V");
        if (this.vornamenIndex < 0) {
            System.out.println("Falsche oder fehlende Eingabe für die Vornamenspalte");
            System.exit(0);
        }
        this.wunschIndizes = new ArrayList<>();
        int num = 1;
        while (this.regex.contains("" + num)) {
            this.wunschIndizes.add(this.regex.indexOf("" + num));
            num++;
        }
        if (num == 1) {
            System.out.println("Falsche oder fehlende Eingabe für die Projektwünsche. Mindestens ein Wunsch wird benötigt!");
            System.exit(0);
        }
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
    private Projekt getProjekt(String id) {
        for (Projekt p : this.projektListe) {
            if (p.getId().equals(id)) {
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
        schuelerList = new ArrayList<>();
        String line;
        Schueler aktSchueler;
        // int i = 0;
        int count = 0;
        try {
            A:
            while ((line = reader.readLine()) != null) {
                count++;
                String[] elemente = line.split(this.delimiter);
                String klasse = elemente[this.klassenIndex];
                String name = elemente[this.namenIndex];
                String vorname = elemente[this.vornamenIndex];
                String[] strWuensche = new String[this.wunschIndizes.size()];
                //System.out.println("Klasse: "+klasse);
                //System.out.println("Name: "+name);
                for (int i = 0; i < strWuensche.length; i++) {
                    strWuensche[i] = elemente[this.wunschIndizes.get(i)];
                    //System.out.println("Wunsch "+strWuensche[i]);
                }
                int intKlasse = -1;
                // System.out.println("Klasse: "+klasse+ " Name: "+name+
                // " Vorname: "+vorname);
                Projekt[] wuensche = new Projekt[strWuensche.length];
                try {
                    for (int i = 0; i < wuensche.length; i++) {
                        wuensche[i] = getProjekt(strWuensche[i]);
                    }
                    for (Projekt p : wuensche) {
                        if (p == null) {
                            System.out.println("Skip Invalid Wunsch in Zeile " + count);
                            continue A;
                        }
                    }
                    if (klasse.equalsIgnoreCase("X")) {
                        intKlasse = 99;
                    } else {
                        intKlasse = Integer.parseInt(klasse.toLowerCase()
                                .replaceAll("[a-z]*", ""));
                    }
                    aktSchueler = new Schueler(count, name, vorname, klasse,
                            wuensche, intKlasse);
                    schuelerList.add(aktSchueler);

                } catch (Exception e) {
                    System.out.println("Fehler bei int-cast in Zeile " +
                            count);
                    //e.printStackTrace();
                }

            }
            System.out.println("Anzahl der Schueler mit korrekter Abgabe, und ohne Sonderprojekt: " + schuelerList.size());
        } catch (Exception e) {
            e.printStackTrace();
        }

    }

}
