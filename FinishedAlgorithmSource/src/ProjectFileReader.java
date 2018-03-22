import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.util.ArrayList;


public class ProjectFileReader {
    /**
     * Bufferedreader des Dokumentes
     */
    private BufferedReader reader;
    /**
     * Liste aller Projekte, die gelesen wurden
     */
    private ArrayList<Projekt> projektList;
    /**
     * String zur Festlegung des Aufbaus der CSV-Datei. Beispielsweise "I;Mm" , dafuer, dass in der ersten Spalte die IDs der Projekte stehen, die zweite Spalte uebersprungen wird, die 3Spalte die maximal Anzahl der Teilnehmer enthaelt und die 4.Spalte die minimal Anzahl der Teilnehmer fuer das Projekt enthaelt.
     */
    private String regex;

    /**
     * Klasse zum Auslesen einer CSV-Datei nach Projekten
     * Initialisiert BufferedReader
     *
     * @param source Path zu der Datei
     * @param regex  String zur Festlegung des Aufbaus der CSV-Datei
     */
    public ProjectFileReader(String source, String regex) {
        try {
            this.regex = regex;
            this.reader = new BufferedReader(new FileReader(source));
        } catch (Exception e) {
            this.reader = null;
            System.out.println(e.getMessage());
        }
    }

    /**
     * Liest alle Projekte und gibt die Liste aller gelesenen Projekte zurueck
     *
     * @return Liste der Projekte
     */
    public ArrayList<Projekt> getProjekte() {
        if (projektList != null) {
            return projektList;
        } else {
            System.out.println("Lade Projekte");
            loadProjekte();
            return projektList;
        }
    }

    /**
     * Liest alle Projekte und initialisiert die Objekte
     */
    private void loadProjekte() {
        projektList = new ArrayList<Projekt>();
        String line;
        Projekt aktProjekt;

        try {
            reader = new BufferedReader(new FileReader("projekte.csv"));
        } catch (Exception e) {
            reader = null;
            System.out.println(e.getMessage());
            System.exit(0);
            return;
        }


        try {
            //Indizes
            int id_index = this.regex.indexOf("I");
            int max_index = this.regex.indexOf("M");
            int min_index = this.regex.indexOf("m");
            if(id_index==-1 || max_index ==-1||min_index==-1){
            	System.out.println("Falsche Eingabe");
            	System.exit(0);
            }
            //Auslesen der Datei
            while ((line = reader.readLine()) != null) {
                String[] elements = line.split(",");

                int id = Integer.parseInt(elements[id_index]);
                int max = Integer.parseInt(elements[max_index]);
                int min = Integer.parseInt(elements[min_index]);
                //Erstellen des Objektes und hinzufuegen zur Liste
                aktProjekt = new Projekt(id, min, max);
                projektList.add(aktProjekt);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

}