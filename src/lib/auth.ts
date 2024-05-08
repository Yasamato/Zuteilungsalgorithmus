import ldap from "ldapjs";
import {
  getServerSession,
  type DefaultSession,
  type NextAuthOptions,
} from "next-auth";
import CredentialsProvider from "next-auth/providers/credentials";
import { env } from "~/env";
import { customAlphabet } from "nanoid";
const nanoid = customAlphabet("1234567890abcdef", 8);

export type UserRole = "admin" | "teachers" | "students";

/**
 * Module augmentation for `next-auth` types. Allows us to add custom properties to the `session`
 * object and keep type safety.
 *
 * @see https://next-auth.js.org/getting-started/typescript#module-augmentation
 */
declare module "next-auth" {
  interface Session extends DefaultSession {
    user: {
      uid: string;
      role: UserRole;
      klasse: string;
      stufe: number;
      vorname: string;
      nachname: string;
    };
  }

  interface User {
    id: string;
    role: UserRole;
    klasse: string;
    stufe: number;
    vorname: string;
    nachname: string;
  }
}

/**
 * Options for NextAuth.js used to configure adapters, providers, callbacks, etc.
 *
 * @see https://next-auth.js.org/configuration/options
 */
export const authOptions: NextAuthOptions = {
  callbacks: {
    session: ({ session }) => session,
  },
  providers: [
    CredentialsProvider({
      name: "LDAP",
      credentials: {
        username: { label: "DN", type: "text", placeholder: "" },
        password: { label: "Password", type: "password" },
      },
      async authorize(credentials, _) {
        if (!credentials)
          return new Promise((_, reject) => reject("No credentials provided"));

        // You might want to pull this call out so we're not making a new LDAP client on every login attemp
        const client = ldap.createClient({
          url: env.LDAP_URI,
          bindDN: env.LDAP_BASE_DN,
        });

        // Essentially promisify the LDAPJS client.bind function
        return new Promise((resolve, reject) => {
          if (
            credentials.username == env.ADMIN_USER &&
            credentials.password == env.ADMIN_PASSWORD
          ) {
            resolve({
              id: "admin",
              role: "admin",
              klasse: "",
              stufe: 0,
              vorname: "",
              nachname: "Admin",
            });
          } else if (env.NODE_ENV !== "production") {
            // if in test environment use dummy accounts
            if (
              credentials.username == "lehrer" &&
              credentials.password == "lehrer"
            ) {
              const id = nanoid();
              resolve({
                id: "test-lehrer-" + id,
                role: "teachers",
                klasse: "",
                stufe: 0,
                vorname: id,
                nachname: "Lehrer",
              });
            } else if (credentials.username == "schüler") {
              const id = nanoid();
              resolve({
                id: "test-student-" + id,
                role: "students",
                klasse: credentials.password,
                stufe: parseInt((credentials.password.match(/\d+/) ?? [""])[0]),
                vorname: id,
                nachname: "Schüler",
              });
            }
          }

          client.bind(
            credentials.username,
            credentials.password,
            (error, res) => {
              if (error) {
                console.error("Failed");
                return reject();
              }

              if (
                typeof res !== "object" ||
                Array.isArray(res) ||
                res === null
              ) {
                console.error("Unkown data returned from ldap " + res);
                return reject();
              }
              const tmp = res as {
                homedirectory: string[];
                uid: string[];
                givenname: string[];
                sn: string[];
              };
              if (
                !Object.hasOwn(tmp, "homedirectory") ||
                !Array.isArray(tmp.homedirectory) ||
                tmp.homedirectory.length == 0 ||
                !Object.hasOwn(tmp, "uid") ||
                !Array.isArray(tmp.uid) ||
                tmp.uid.length == 0 ||
                !Object.hasOwn(tmp, "givenname") ||
                !Array.isArray(tmp.givenname) ||
                tmp.givenname.length == 0 ||
                !Object.hasOwn(tmp, "sn") ||
                !Array.isArray(tmp.sn) ||
                tmp.sn.length == 0
              ) {
                console.error("Unkown data returned from ldap " + res);
                return reject();
              }

              // extract user information
              const uid = tmp.uid[0];
              if (!uid) {
                console.error("Could not extract user id from response " + res);
                return reject();
              }
              const vorname = tmp.givenname[0];
              if (!vorname) {
                console.error(
                  "Could not extract givenname from response " + res,
                );
                return reject();
              }
              const nachname = tmp.sn[0];
              if (!nachname) {
                console.error("Could not extract name from response " + res);
                return reject();
              }

              const path = (tmp.homedirectory[0] ?? "").split("/");

              let klasse = "";
              let stufe = 0;
              switch (path[2] as UserRole) {
                case "students": {
                  const pathKlasse = path[3];
                  if (!pathKlasse) {
                    console.error(
                      "Cannot extract class of student from ldap with homedirectory " +
                        tmp.homedirectory[0],
                    );
                    return reject();
                  }
                  klasse = pathKlasse;
                  stufe = parseInt((pathKlasse.match(/\d+/) ?? [""])[0]);
                  break;
                }
                case "teachers": {
                  break;
                }
                case "admin": {
                  break;
                }
                default: {
                  console.error(
                    "Unkown user role " +
                      path[2] +
                      " from homedirectory " +
                      tmp.homedirectory[0],
                  );
                  return reject();
                }
              }

              console.log("Logged in");
              resolve({
                id: uid,
                role: path[2] as UserRole,
                klasse: klasse,
                stufe: stufe,
                vorname: vorname,
                nachname: nachname,
              });
            },
          );
        });
      },
    }),
    /**
     * ...add more providers here.
     *
     * Most other providers require a bit more work than the Discord provider. For example, the
     * GitHub provider requires you to add the `refresh_token_expires_in` field to the Account
     * model. Refer to the NextAuth.js docs for the provider you want to use. Example:
     *
     * @see https://next-auth.js.org/providers/github
     */
  ],
};

/**
 * Wrapper for `getServerSession` so that you don't need to import the `authOptions` in every file.
 *
 * @see https://next-auth.js.org/configuration/nextjs
 */
export const getServerAuthSession = () => getServerSession(authOptions);
