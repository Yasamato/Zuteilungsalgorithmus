import "~/app/globals.css";

export const metadata = {
  title: "AG Wahlen",
  description: "Zuteilungsalgorithmus für die Wahlen von AGs",
  icons: [{ rel: "icon", url: "/favicon.ico" }],
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="de">
      <head>
        <meta charSet="utf-8" />
        <meta
          name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no"
        />

        <meta
          name="github"
          content="https://github.com/Yasamato/Zuteilungsalgorithmus/"
        />
        <meta name="author" content="Leo Jung" />
        <meta name="author" content="Fabian von der Warth" />
      </head>
      <body className="flex min-h-screen flex-col">
        <nav className="bg-blue-900 p-4 text-white">Ich bin eine Navbar</nav>
        <main className="flex grow flex-col items-center justify-center">
          {children}
        </main>
        <footer className="bg-blue-900 p-4 text-white">
          Ich bin ein Footer
        </footer>
      </body>
    </html>
  );
}
